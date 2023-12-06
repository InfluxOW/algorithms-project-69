install:
	composer install
test:
	composer exec phpunit
lint:
	composer exec phpcs --verbose
lint-fix:
	composer exec phpcbf --verbose
analyse:
	composer exec phpstan analyse --verbose
