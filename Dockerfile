FROM php:7.2-cli
WORKDIR
EXPOSE 8081
VOLUME $APP_DIR
CMD ["php", "-S", "0.0.0.0:80", "-t", "./web", "./web/app.php"]