.PHONY: *

## —— Help ————————————————————————————————————
help: ## Show help
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Tests ———————————————————————————————————
tests: ## Run tests
	rm -rf $(shell php -r "echo sys_get_temp_dir();")/com.github.easycorp.easyadmin/tests/var/test/cache/*
	SYMFONY_DEPRECATIONS_HELPER='ignoreFile=./tests/baseline-ignore.txt' php vendor/bin/simple-phpunit -v
	rm -rf $(shell php -r "echo sys_get_temp_dir();")/com.github.easycorp.easyadmin/tests/var/pretty_urls/test/cache/*
	USE_PRETTY_URLS=1 php vendor/bin/simple-phpunit tests/Controller/PrettyUrls/PrettyUrlsController.php
tests-coverage: ## Generate test coverage
	rm -rf $(shell php -r "echo sys_get_temp_dir();")/com.github.easycorp.easyadmin/tests/var/test/cache/*
	XDEBUG_MODE=coverage php vendor/bin/simple-phpunit --coverage-html $(shell php -r "echo sys_get_temp_dir();")/com.github.easycorp.easyadmin/tests/var/test/coverage/
tests-coverage-view-in-browser: ## Open the generated HTML coverage in your default browser
	sensible-browser "file://$(shell php -r "echo sys_get_temp_dir();")/com.github.easycorp.easyadmin/tests/var/test/coverage/index.html"

## —— Linters —————————————————————————————————
linter-code-syntax: ## Lint PHP code (in dry-run mode, does not edit files)
	docker run --rm -it --pull always -w=/app -v $(shell pwd):/app oskarstark/php-cs-fixer-ga:latest --diff -vvv --dry-run --using-cache=no
linter-docs: ## Lint docs
	docker run --rm -it --pull always -e DOCS_DIR='/docs' -v $(shell pwd)/doc:/docs oskarstark/doctor-rst:latest --short

## —— Development —————————————————————————————
build: ## Initially build the package before development
	composer update
	yarn install

build-assets: ## Rebuild assets after changes in JS or SCSS
	yarn encore production
	php ./src/Resources/bin/fix-assets-manifest-file.php

checks-before-pr: linter-code-syntax linter-docs tests ## Runs tests and linters which are also run on PRs
