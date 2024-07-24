#!/bin/bash

# -------------------------------------------
# TARGETS
# -------------------------------------------
help: ## Show help
	@echo -e '\e[33mUsage:\e[0m'
	@echo '  make [target]'
	@echo
	@echo -e '\e[33m Targets:'
	@awk 'BEGIN {FS = ":.*##"; printf "\033[36m\033[0m"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
# -------------------------------------------

TITLE=\n\033[1;32m
SEPARATOR=\033[1;32m
END=\033[0m

setup-dev: ## Sets the application up for deveopment
	@echo "$(TITLE)Installing symfony dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	composer install

	@echo "$(TITLE)Installing node dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	npm install

	@echo "$(TITLE)Executing Webpack dev$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	npm run dev


setup-prod: ## Sets the application up for production
	@echo "$(TITLE)Installing symfony dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	APP_ENV=prod APP_DEBUG=false composer install

	@echo "$(TITLE)Installing node dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	npm install

	@echo "$(TITLE)Security: generating keys$(END)"
	@echo "$(SEPARATOR)--------------------------------------------$(END)"
	bin/console secrets:generate-keys --env=prod

	@echo "$(TITLE)Security: reCaptcha - RECAPTCHA3_KEY$(END)"
	@echo "$(SEPARATOR)--------------------------------------------$(END)"
	bin/console secrets:set RECAPTCHA3_KEY --env=prod

	@echo "$(TITLE)Security: reCaptcha - RECAPTCHA3_SECRET$(END)"
	@echo "$(SEPARATOR)--------------------------------------------$(END)"
	bin/console secrets:set RECAPTCHA3_SECRET --env=prod

	@echo "$(TITLE)Optimizing environment variables$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	APP_ENV=prod APP_DEBUG=false composer dump-env prod

	@echo "$(TITLE)Optimizing JS, CSS, assets$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	npm run build

	@echo "$(TITLE)Removing NPM development dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	npm prune --production

	@echo "$(TITLE)Removing Composer development dependecies$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	APP_ENV=prod APP_DEBUG=false composer install --no-dev --optimize-autoloader

	@echo "$(TITLE)Removing devlopment files$(END)"
	@echo "$(SEPARATOR)------------------------------$(END)"
	rm .env.dev
	rm .env.test
	rm .gitignore
	rm .php-cs-fixer.dist.php
	rm jsconfig.json
	rm phpstan.neon
	rm phpunit.xml.dist
	rm webpack.config.js