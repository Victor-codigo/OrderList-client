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

TITLE=\033[1;32m
TITLE_END=\033[0m

setup-dev: ## Sets the application up for deveopment
	@echo "\n$(TITLE)Installing symfony dependecies$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	composer install

	@echo "\n$(TITLE)Installing node dependecies$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	npm install

	@echo "$(TITLE)Executing Webpack dev$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	npm run dev


setup-prod: ## Sets the application up for production
	@echo "$(TITLE)Installing symfony dependecies$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	composer install

	@echo "$(TITLE)Installing node dependecies$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	npm install

	@echo "$(TITLE)Security: generating keys$(TITLE_END)"
	@echo "$(TITLE)--------------------------------------------$(TITLE_END)"
	bin/console secrets:generate-keys --env=prod

	@echo "$(TITLE)Security: reCaptcha - RECAPTCHA3_KEY$(TITLE_END)"
	@echo "$(TITLE)--------------------------------------------$(TITLE_END)"
	bin/console secrets:set RECAPTCHA3_KEY --env=prod

	@echo "$(TITLE)Security: reCaptcha - RECAPTCHA3_SECRET$(TITLE_END)"
	@echo "$(TITLE)--------------------------------------------$(TITLE_END)"
	bin/console secrets:set RECAPTCHA3_SECRET --env=prod

	@echo "$(TITLE)Optimizing environment variables$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	composer dump-env prod

	@echo "$(TITLE)Optimizing environment variables$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	npm run build

	@echo "$(TITLE)Removing node dependecies$(TITLE_END)"
	@echo "$(TITLE)------------------------------$(TITLE_END)"
	npm prune --production
