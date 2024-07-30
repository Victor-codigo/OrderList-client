# OrderList client
OrderList client it is a client for OrderList-api.

# Prerequisites
- Docker
- Or if you prefer to create your own configuration:
	- HTTP server,
	- PHP 8.3

# Stack

- [Docker](https://www.docker.com/)
- [PHP 8.3](https://www.php.net/)
- [PHPUnit 9.6](https://phpunit.de/index.html)
- [Symfony 6.4](https://symfony.com/)
- [Twig 3](https://twig.symfony.com/)
- [Composer](https://getcomposer.org/)
- [Javascript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- [Stimulus framework](https://stimulus.hotwired.dev/)
- [Bootstarp 5.3](https://getbootstrap.com/)
- [SASS](https://sass-lang.com/)

# Tools
- [VSCode](https://code.visualstudio.com/)

# Installation
1. [Fork](https://github.com/Victor-codigo/OrderList-client/fork) or clone repository.
```
git clone git@github.com:Victor-codigo/OrderList-client.git
```

2.  Under folder .docker is all docker configuration. There is a make file with the following commands:

- `make up` Rise up the containers
- `make build-cache` Builds containers with cache
- `make down` Set down containers
- `make start`Starts containers
- `make stop` Stops containers
- `make restart` Restart containers
- `make ps` List containers
- `make logs`  Show logs
- `make down-build-up` Set down, builds and rise up containers
- `make bash` Execute bash in php container
- `make root` Execute bash in php container as root

Build and start containers
```
make up
```
The following containers will be built up:

-   Nginx
-   PHP
-   Composer

3. Enter inside php container as developer user:
```
make bash
```
4.  There is a make file with following commands:
   - `make setup-dev` Sets the application up for development
   - `make setup-prod` Sets the application up for production

Execute the following command to build client for development:
```
make setup-dev
```
Or build client for production:
```
make setup-prod
```

5.  Follow make instructions.
6.  Congratulations! You have installed the client correctly
7.  You can access client though:
-   [http://127.0.0.1](http://127.0.0.1) to client
- It is necessary api proxy-server to be running before.
