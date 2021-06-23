DIR := ${CURDIR}
QA_IMAGE := jakzal/phpqa

phpstan:
	docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyze

phpstan-baseline:
	docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyze --error-format baselineNeon > phpstan-baseline.neon

test:
	composer update --prefer-dist --no-interaction ${COMPOSER_PARAMS}
	vendor/bin/phpunit

test-lowest:
	COMPOSER_PARAMS='--prefer-lowest' $(MAKE) test

codestyle:
	docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) php-cs-fixer fix
