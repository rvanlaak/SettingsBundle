DIR := ${CURDIR}
QA_IMAGE := jakzal/phpqa

phpstan:
	docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyze

phpstan-baseline:
	docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyze --error-format baselineNeon > phpstan-baseline.neon
