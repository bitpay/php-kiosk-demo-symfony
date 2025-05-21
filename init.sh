#!/bin/bash

if [ ! -e .env.local ] || [ ! -e application.yaml ]; then
    echo "Please read README.md and follow running instructions"
    exit 1
fi

if [ ! -d vendor ]; then
    composer install --no-progress --ignore-platform-reqs
fi

if [ ! -e *.sqlite ]; then
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force

    echo "SQLite database created successfully"
fi

echo "All good, the project is initialized!"