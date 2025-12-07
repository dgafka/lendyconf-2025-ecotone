#!/bin/bash

set -e;

bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:pool:clear cache.app
bin/console cache:pool:clear cache.system
