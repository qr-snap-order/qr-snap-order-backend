# セットアップ系コマンド

setup:
	cp .env.example .env
	@make composer-install
	@make up
	@make ide-helper-models
	@make migrate
	@make seed
	@make s3-create-bucket
	@make passport-install

composer-install:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs

ide-helper-models:
	./vendor/bin/sail artisan ide-helper:models --nowrite

passport-install:
	# Please set client secret to .env
	./vendor/bin/sail artisan passport:install

# コンテナ操作系コマンド

up:
	./vendor/bin/sail up -d

start:
	./vendor/bin/sail start

stop:
	./vendor/bin/sail stop

down:
	./vendor/bin/sail down

destroy:
	./vendor/bin/sail down -v

# マイグレーション系コマンド

migrate:
	./vendor/bin/sail artisan migrate

seed:
	./vendor/bin/sail artisan db:seed

fresh:
	./vendor/bin/sail artisan migrate:fresh

rollback:
	./vendor/bin/sail artisan migrate:rollback --step=1

# テスト系コマンド

test:
	./vendor/bin/sail test

debug-test:
	./vendor/bin/sail debug test

# Linter系コマンド

lint:
	./vendor/bin/sail bin phpstan analyse

# S3系コマンド

s3:
	docker exec localstack-main awslocal s3api $(COMMAND)

s3-create-bucket:
	@make s3 COMMAND="create-bucket --bucket develop-public"

s3-bucket-objects:
	@make s3 COMMAND="list-objects --bucket develop-public"
