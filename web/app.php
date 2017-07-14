<?php
use Application\Command\AccountChangeName;
use Application\Command\AddMoney;
use Application\Command\CreateAccount;
use Application\Handler\AccountChangeNameHandler;
use Application\Handler\AddMoneyHandler;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Schema\SchemaException;
use Domain\Account;
use Domain\Event\AccountChangedName;
use Domain\Event\AccountCreated;
use Domain\Event\MoneyAdded;
use Infrastructure\EventSourcedAccountRepository;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Prooph\EventStore\Adapter\PayloadSerializer\JsonPayloadSerializer;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\EventStoreBusBridge\TransactionManager;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Rhumsaa\Uuid\Uuid;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$app = new Application();
$app->register(new \Silex\Provider\SerializerServiceProvider());
$app->register(new Sorien\Provider\PimpleDumpProvider());

$app['debug'] = DEBUG_MODE;
$app['db_connection'] = function () {
    $config = new Configuration();
    $connectionParams = array(
        'host' => DB_HOST,
        'port' => DB_PORT,
        'user' => DB_USER,
        'password' => DB_PASS,
        'dbname' => DB_NAME,
        'driver' => 'pdo_mysql'
    );
    $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    $schema = $connection->getSchemaManager()->createSchema();
    return $connection;
};
$app['event_bus'] = function () {
    return new EventBus();
};
$app['event_store'] = function (Application $app) {
    $eventStore = new EventStore(
        new DoctrineEventStoreAdapter(
            $app['db_connection'],
            new FQCNMessageFactory(),
            new NoOpMessageConverter(),
            new JsonPayloadSerializer()
        ),
        new ProophActionEventEmitter()
    );

    $ep = (new EventPublisher($app['event_bus']));
    $ep->setUp($eventStore);

    $schema = $app['db_connection']->getSchemaManager()->createSchema();

    try {
        EventStoreSchema::createSingleStream($schema, 'event_stream', true);
        foreach ($schema->toSql($app['db_connection']->getDatabasePlatform()) as $sql) {
            $app['db_connection']->exec($sql);
        }
    } catch (SchemaException $e) {

    }

    return $eventStore;
};
$app['repo_account'] = function (Application $app) {
    $accountRepository = new EventSourcedAccountRepository(
        new AggregateRepository(
            $app['event_store'],
            AggregateType::fromAggregateRootClass(Account::class),
            new AggregateTranslator()
        )
    );
    return $accountRepository;
};
$app['transaction_manager'] = function (Application $app) {
    $transactionManager = new TransactionManager();
    $transactionManager->setUp($app['event_store']);
    return $transactionManager;
};
$app['command_bus'] = function (Application $app) {
    $commandBus = new CommandBus();
    $commandBus->utilize($app['transaction_manager']);
    return $commandBus;
};
$app['event_router'] = function (Application $app) {
    $eventRouter = new EventRouter();
    $eventRouter
        ->route(AccountCreated::class)
        ->to(function (AccountCreated $event) {
            //var_dump('CREATED: ' . $event->currency());
        });
    $eventRouter
        ->route(MoneyAdded::class)
        ->to(function (MoneyAdded $event) {
            //var_dump('LOADED: ' . $event->amount());
        });
    $eventRouter
        ->route(AccountChangedName::class)
        ->to(function (AccountChangedName $event) {
            //var_dump('CHANGED NAME: ' . $event->getName());
        });

    $eventRouter->attach($app['event_bus']->getActionEventEmitter());
    return $eventRouter;
};

//Routing
$commandRouter = new CommandRouter();
$commandRouter->attach($app['command_bus']->getActionEventEmitter());
$commandRouter
    ->route(CreateAccount::class)
    ->to(new \Application\Handler\CreateAccountHandler($app['repo_account']));
$commandRouter
    ->route(AddMoney::class)
    ->to(new AddMoneyHandler($app['repo_account']));
$commandRouter
    ->route(AccountChangeName::class)
    ->to(new AccountChangeNameHandler($app['repo_account']));

$app['event_router']
    ->route(AccountCreated::class)
    ->to(function (AccountCreated $event) {
        //var_dump('CREATED: ' . $event->currency());
    });
$app['event_router']
    ->route(MoneyAdded::class)
    ->to(function (MoneyAdded $event) {
        //var_dump('LOADED: ' . $event->amount());
    });
$app['event_router']
    ->route(AccountChangedName::class)
    ->to(function (AccountChangedName $event) {
        //var_dump('CHANGED NAME: ' . $event->getName());
    });

$app->post('/accounts', function (Application $app, Request $request){

    $uuid = Uuid::uuid4();
    $currency = $request->get('currency');
    $app['command_bus']->dispatch(new CreateAccount($uuid, $currency));
    return new Response('', Response::HTTP_CREATED, ['location' => $app['url_generator']->generate('getAccountByUuid', ['uuid' => $uuid->toString()])]);
});

$app->get('/accounts/{uuid}', function (Application $app, Request $request, $uuid) {
    if(!Uuid::isValid($uuid)) {
        return new Response( 'Invalid uuid', Response::HTTP_BAD_REQUEST);
    }
    $uuid = Uuid::fromString($uuid);
    $format = $request->getRequestFormat('json');

    /** @var Account $account */
    $repoAccount = $app['repo_account'];
    $account = $repoAccount->get($uuid);
    if(!$account instanceof Account) {
        return new Response( 'There is no account with this uuid', Response::HTTP_NO_CONTENT);
    }

    $result = $app['serializer']->serialize($account, $format);
    return new Response($result);
})->bind('getAccountByUuid');

$app->run();