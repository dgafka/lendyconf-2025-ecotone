.PHONY: build up down logs test tests shell exec help

# Colors for pretty output
GREEN  := \033[0;32m
YELLOW := \033[0;33m
CYAN   := \033[0;36m
RED    := \033[0;31m
RESET  := \033[0m
BOLD   := \033[1m

CONTAINER_NAME := ecotone_demo

help: ## Show this help message
	@echo ""
	@echo "$(BOLD)$(CYAN)Available commands:$(RESET)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(RESET) %s\n", $$1, $$2}'
	@echo ""

build: ## Build Docker images
	@echo ""
	@echo "$(BOLD)$(CYAN)Building Docker images...$(RESET)"
	@echo ""
	@docker-compose build
	@echo ""
	@echo "$(BOLD)$(GREEN)Docker images have been built!$(RESET)"
	@echo ""

up: ## Start all containers in detached mode
	@echo ""
	@echo "$(BOLD)$(CYAN)Starting Docker containers...$(RESET)"
	@echo ""
	@docker-compose up -d
	@echo ""
	@echo "$(BOLD)$(GREEN)Containers are up and running!$(RESET)"
	@echo ""

down: ## Stop and remove all containers
	@echo ""
	@echo "$(BOLD)$(CYAN)Stopping Docker containers...$(RESET)"
	@echo ""
	@docker-compose down
	@echo ""
	@echo "$(BOLD)$(GREEN)Containers have been stopped and removed.$(RESET)"
	@echo ""

logs: ## Follow container logs
	@docker logs -f $(CONTAINER_NAME)

test: ## Run all tests with colors and formatted output
	@echo ""
	@echo "$(BOLD)$(CYAN)════════════════════════════════════════════════════════════$(RESET)"
	@echo "$(BOLD)$(CYAN)                    Running Tests                           $(RESET)"
	@echo "$(BOLD)$(CYAN)════════════════════════════════════════════════════════════$(RESET)"
	@echo ""
	@docker exec -it $(CONTAINER_NAME) composer test:setup
	@docker exec -it $(CONTAINER_NAME) php vendor/bin/phpunit --colors=always --testdox
	@echo ""
	@echo "$(BOLD)$(GREEN)════════════════════════════════════════════════════════════$(RESET)"
	@echo "$(BOLD)$(GREEN)                    Tests Completed                         $(RESET)"
	@echo "$(BOLD)$(GREEN)════════════════════════════════════════════════════════════$(RESET)"
	@echo ""

tests: test ## Alias for 'test'

shell: ## Open an interactive shell in the container
	@echo ""
	@echo "$(BOLD)$(CYAN)Connecting to container $(YELLOW)$(CONTAINER_NAME)$(CYAN)...$(RESET)"
	@echo ""
	@docker exec -it $(CONTAINER_NAME) /bin/bash

exec: ## Execute a command in the container (usage: make exec CMD="command")
ifndef CMD
	@echo ""
	@echo "$(RED)Error: CMD is required$(RESET)"
	@echo ""
	@echo "$(YELLOW)Usage:$(RESET) make exec CMD=\"your command\""
	@echo ""
	@echo "$(YELLOW)Examples:$(RESET)"
	@echo "  make exec CMD=\"bin/console cache:clear\""
	@echo "  make exec CMD=\"composer install\""
	@echo ""
	@exit 1
else
	@echo ""
	@echo "$(BOLD)$(CYAN)Executing:$(RESET) $(YELLOW)$(CMD)$(RESET)"
	@echo ""
	@docker exec -it $(CONTAINER_NAME) $(CMD)
endif

