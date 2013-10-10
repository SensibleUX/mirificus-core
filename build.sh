#! /bin/bash

composer install
# need graphviz for phpdoc
sudo apt-get install graphviz
# run phpdoc
vendor/bin/phpdoc.php -d src/Mirificus -t doc/
