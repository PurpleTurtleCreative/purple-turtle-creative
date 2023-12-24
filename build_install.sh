#!/bin/bash

# There's only dev dependencies from Composer at the moment.
# composer install --optimize-autoloader --no-dev

npm ci --no-audit
npm run build
