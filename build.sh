#! /bin/bash

composer install
# need graphviz for phpdoc
apt-get install graphviz
# run phpdoc
vendor/bin/phpdoc.php -d src/Mirificus -t doc/
