update:
	git pull && docker-compose build && docker-compose stop && docker-compose up -d && docker-compose exec web composer install