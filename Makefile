COMPOSE=docker-compose
PHP=$(COMPOSE) exec php
CONSOLE=$(PHP) bin/console
COMPOSER=$(PHP) composer

up:
	@${COMPOSE} up -d

down:
	@${COMPOSE} down

clear:
	@${CONSOLE} cache:clear

migration:
	@${CONSOLE} make:migration

migrate:
	@${CONSOLE} doctrine:migrations:migrate

fixtload:
	@${CONSOLE} doctrine:fixtures:load

require:
	@${COMPOSER} require $2

encore_dev:
	@${COMPOSE} run node yarn encore dev

encore_prod:
	@${COMPOSE} run node yarn encore production

phpunit:
	@${PHP} bin/phpunit

env_create:
	touch testfile.txt
	echo "TRUSTED_HOSTS='^billing\.study-on\.local$$'" >> testfile.txt
	echo "APP_SECRET=0dda54fa9f555683811a0b3e0b45ae88" >> testfile.txt
	echo "DATABASE_URL=pgsql://pguser:pguser@study-onbilling_database_1:5432/study_on_billing" >> testfile.txt
	echo "JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem" >> testfile.txt
	echo "JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem" >> testfile.txt
	echo "JWT_PASSPHRASE=10hFsNk4Ra3kYHStaI0I" >> testfile.txt
	echo "MAILER_DSN=smtp://mailhog:1025" >> testfile.txt

composer_install:
	${COMPOSER} install

install: env_create up composer_install
