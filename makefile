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


setup-dev: ## Sets the application up for deveopment
	@echo 'Installing symfony dependecies'
	@echo '------------------------------'
	composer install

	@echo 'Installing node dependecies'
	@echo '------------------------------'
	npm install

	@echo 'Migrating database, dev and test environments'
	@echo '--------------------------------------------'
	bin/console doctrine:database:create --env=dev
	bin/console doctrine:database:create --env=test
	bin/console doctrine:migrations:migrate --no-interaction --env=dev
	bin/console doctrine:migrations:migrate --no-interaction --env=test


setup-prod: ## Sets the application up for production
	@echo 'Installing symfony dependecies'
	@echo '------------------------------'
	composer install

	@echo 'Installing node dependecies'
	@echo '------------------------------'
	npm install --omit=dev

	@echo 'Optimizing environment variables'
	@echo '------------------------------'
	composer dump-env prod

	@echo 'Migrating database, prod environments'
	@echo '--------------------------------------------'
	bin/console doctrine:database:create --env=prod
	bin/console doctrine:migrations:migrate --no-interaction --env=prod

	@echo 'Security: generating keys'
	@echo '--------------------------------------------'
	bin/console secrets:generate-keys --env=prod

	@echo 'Security: reCaptcha - RECAPTCHA3_KEY'
	@echo '--------------------------------------------'
	bin/console secrets:set RECAPTCHA3_KEY --env=prod

	@echo 'Security: reCaptcha - RECAPTCHA3_SECRET'
	@echo '--------------------------------------------'
	bin/console secrets:set RECAPTCHA3_SECRET --env=prod