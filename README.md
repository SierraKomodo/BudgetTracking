# Budget Tracking

## Installation

- Install composer packages: `composer install`
- Generate Doctrine proxies: `./bin/doctrine orm:generate-proxies`
- Install database schema: `./bin/doctrine orm:schema-tool:update --complete --force`

## Validation

- Validate schema: `./bin/doctrine orm:validate-schema`
