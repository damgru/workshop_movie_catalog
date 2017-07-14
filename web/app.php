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

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
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
$app['repo_movies'] = function (Application $app) {
    $moviesRepository = new \Infrastructure\EventSourcedMoviesRepository(
        new AggregateRepository(
            $app['event_store'],
            AggregateType::fromAggregateRootClass(\Domain\Movie::class),
            new AggregateTranslator()
        )
    );
    return $moviesRepository;
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
        ->route(\Domain\Event\MovieAdded::class)
        ->to(function (\Domain\Event\MovieAdded $event) {
            //var_dump('CREATED: ' . $event->currency());
        });

    $eventRouter->attach($app['event_bus']->getActionEventEmitter());
    return $eventRouter;
};

###############################################3
## COMMANDS

$commandRouter = new CommandRouter();
$commandRouter->attach($app['command_bus']->getActionEventEmitter());
$commandRouter
    ->route(\Application\Command\AddMovie::class)
    ->to(function(\Application\Command\AddMovie $command) use ($app){
        $app['repo_movies']->AddMovie(\Domain\Movie::new(
            $command->getUuid(), $command->getName(), $command->getImg(), $command->getUrl()
        ));
    });
$commandRouter
    ->route(\Application\Command\DeleteMovie::class)
    ->to(function(\Application\Command\DeleteMovie $command) use ($app){
        $app['repo_movies']->DeleteMovieByUuid($command->getUuid());
    });

###########################################
## EVENTY

$app['event_router']
    ->route(\Domain\Event\MovieAdded::class)
    ->to(function (\Domain\Event\MovieAdded $event) {

    });
$app['event_router']
    ->route(\Domain\Event\MovieDeleted::class)
    ->to(function (\Domain\Event\MovieDeleted $event) {

    });
$app['event_router']
    ->route(\Domain\Event\MovieWatched::class)
    ->to(function (\Domain\Event\MovieWatched $event) {

    });

##################################
## ROUTING

$app->match('/', function (Application $app){
    return new Response('Hello');
});

$app->post('/movies', function (Application $app, Request $request){
    $uuid = Uuid::uuid4();
    $name = $request->get('name');
    $img = $request->get('img');
    $url = $request->get('url');
    $app['command_bus']->dispatch(new \Application\Command\AddMovie($uuid, $name, $img, $url));
    return new Response('', Response::HTTP_CREATED, ['location' => $app['url_generator']->generate('GetMovieByUuid', ['uuid' => $uuid->toString()])]);
});

$app->get('/movies/{uuid}', function (Application $app, Request $request, $uuid) {
    if(!Uuid::isValid($uuid)) {
        return new Response( 'Invalid uuid', Response::HTTP_BAD_REQUEST);
    }
    $uuid = Uuid::fromString($uuid);
    $format = $request->getRequestFormat('json');

    /** @var \Domain\Movie $movie */
    $repo = $app['repo_movies'];
    $movie = $repo->get($uuid);
    if(!$movie instanceof \Domain\Movie) {
        return new Response( 'There is no account with this uuid', Response::HTTP_NO_CONTENT);
    }

    $result = $app['serializer']->serialize($movie, $format);
    return new Response($result);
})->bind('getMovieByUuid');

$app->get('/movies/', function (Application $app){
    /** @var \Domain\MoviesRepository $repo */
    $view = new \Infrastructure\ReadonlyMovies($app['db_connection']);
    $movies = $view->getMovies();
    return new Response(json_encode($movies));
});


$app->run();