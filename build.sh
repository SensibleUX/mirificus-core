#! /bin/bash
composer install
echo "listing directory"
ls
vendor/bin/phpdoc.php -d src/Mirificus -t doc/