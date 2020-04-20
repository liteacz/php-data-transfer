UID=$$(id -u)

# |-------------------------------------------------------------
# | Show all
# |-------------------------------------------------------------
# |
# | Outputs the content of this file so it's little bit clearer
# | what commands are available and what can be run using `make`
all:
	cat Makefile

# |-------------------------------------------------------------
# | Spin up the contianer
# |-------------------------------------------------------------
# |
# | Starts the php-cli container with the `loop` command which is
# | simple infinite loop that keeps the contianer running.
# | That way the container does not get created and destryoed everytime
# | a command is run, so the response latency is little bit lower.
up:
	docker-compose up -d
	docker-compose exec php-cli chown -R $(UID):$(UID) /composer

# |-------------------------------------------------------------
# | Shutdown
# |-------------------------------------------------------------
# |
# | Tears down the container.
down:
	docker-compose down

# |-------------------------------------------------------------
# | Restart
# |-------------------------------------------------------------
# |
# | Restart the container. Usefull for example when environemnt variables
# | change. In order for change to propagate to the container it needs to be recreated.
restart: down up
	@echo "Done"

# |-------------------------------------------------------------
# | Logs
# |-------------------------------------------------------------
# |
# | Show the container logs
logs:
	docker-compose logs php-cli

# |-------------------------------------------------------------
# | Logs follow
# |-------------------------------------------------------------
# |
# | Attaches to the container and outputs the logs
logsf: up
	docker-compose logs -f php-cli

# |-------------------------------------------------------------
# | Shell
# |-------------------------------------------------------------
# |
# | Attaches to the containers shell. Usefull when you need to operate inside the container.
# | You can specify the service (defualt php-cli) and the UID (default your UID) under which to connect.
# | E.g.: When you need to enter the container as the root:
# | make shell u=0
shell: up cmd.sh
	./cmd.sh attach-to-shell $(u) $(s)

# |-------------------------------------------------------------
# | PHPStan
# |-------------------------------------------------------------
# |
# | Runs static analysis using PHPStan
phpstan: up
	docker-compose exec -u $(UID) php-cli composer run-script phpstan

# |-------------------------------------------------------------
# | CodeSniffer
# |-------------------------------------------------------------
# |
# | Runs static analysis using Code Sniffer
phpcs: up
	docker-compose exec -u $(UID) php-cli composer run-script phpcs

# |-------------------------------------------------------------
# | CodeBeautifier
# |-------------------------------------------------------------
# |
# | Fixes the fixable errors from phpcs
phpcbf: up
	docker-compose exec -u $(UID) php-cli composer run-script phpcbf

# |-------------------------------------------------------------
# | Tests
# |-------------------------------------------------------------
# |
# | Executes the PHPUnit tests
test: up cmd.sh
	docker-compose exec -u $(UID) php-cli composer test

# |-------------------------------------------------------------
# | Security
# |-------------------------------------------------------------
# |
# | Runs sensyolabs php security-checker
security:
	docker-compose exec -u $(UID) php-cli composer run-script security

# |-------------------------------------------------------------
# | All static analysis
# |-------------------------------------------------------------
# |
# | Runs all the static analysis. This get's done in the repository CI.
# | Verify that all passes before submitting PR, otherwise it will get rejected.
stan: phpstan phpcs security
	@echo "All static analysis done"