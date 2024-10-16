# Base docker compose template php + postgresql

This docker compose template creates project with
1. nginx
2. php 8.3.1 fpm + xdegug for PHPSTORM
3. postgresql 16
4. composer

## Installation

1. Copy project files to directory with your Symfony project
2. Redefine and move to your .env file variables from the env.docker file. By default it's .env.local
3. If your .env file is not .env.local edit Makefile: change DOCKER_COMP = docker compose --env-file .env.local
4. Open the file .docker/nginx/conf.d/default.conf and change root path root root /${APP_DIRECTORY}/public;
5. Run make start command
6. Go to http://localhost:8001 (by default). You can receive the correct answer