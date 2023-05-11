# Run update/install scripts
composer install

# Regenerate caches
./bin/doctrine orm:generate-proxies
