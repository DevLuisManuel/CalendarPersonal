#!make
initial:
	#Subiendo App y Nginx
	@docker-compose --env-file .env up -d --build

down-all:
	#Parar App y Nginx
	@docker-compose down --remove-orphans