#!make
build:
	@cd ./App && cp .env.example .env
	@make initial
	@make composer
	@make migrate
	@make npm install

initial:
	#Subiendo App y Nginx
	@docker-compose --env-file ../enviroment/.env up -d --build
	@make key

down-all:
	#Parar App y Nginx
	@docker-compose --env-file ../enviroment/.env down --remove-orphans

composer:
	@docker run --rm --interactive --tty \
	  --volume ${PWD}/App:/app \
	  composer i -o

npm:
ifeq (install, $(filter install,$(MAKECMDGOALS)))
	@echo "NPM install"
	@docker run --rm --interactive --tty \
	  --volume ${PWD}/Front:/app \
	  -w /app node:16-alpine \
	  sh -c 'npm install'
endif

ifeq (update, $(filter update,$(MAKECMDGOALS)))
	@echo "NPM update"
	@docker run --rm --interactive --tty \
	  --volume ${PWD}/Front:/app \
	  -w /app node:16-alpine \
	  sh -c 'npm update'
endif

ifeq (build, $(filter build,$(MAKECMDGOALS)))
	@echo "Build to production"
	@docker run --rm --interactive --tty \
	  --volume ${PWD}/Front:/app \
	  -w /app node:16-alpine \
	  sh -c 'npm run build'
endif

migrate:
	@docker-compose --env-file ../enviroment/.env exec app php artisan createDatabase
	@docker-compose --env-file ../enviroment/.env exec app php artisan migrate

key:
	@docker-compose --env-file ../enviroment/.env exec app php artisan key:generate

%:
    @: