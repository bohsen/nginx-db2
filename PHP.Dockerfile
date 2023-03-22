FROM php:fpm

RUN apt-get update -qq > /dev/null && \
    apt-get install unzip
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Documentation
# https://www.ibm.com/docs/en/db2/11.5?topic=dsd-installing-data-server-driver-odbc-cli-software-linux-unix-operating-systems
# https://www.ibm.com/docs/en/db2/11.5?topic=environment-configuring
# https://stackoverflow.com/questions/37066985/php-connection-to-db2
# https://github.com/nagstaku/php_db2
# https://github.com/php/pecl-database-ibm_db2
# https://github.com/php/pecl-database-pdo_ibm

# Install DB2 php-extensions

RUN mkdir -p /opt/ibm/ && curl https://public.dhe.ibm.com/ibmdl/export/pub/software/data/db2/drivers/odbc_cli/linuxx64_odbc_cli.tar.gz | tar -xz -C /opt/ibm/

## set env vars needed for PECL install
ENV IBM_DB_HOME=/opt/ibm/clidriver
ENV LD_LIBRARY_PATH=/opt/ibm/clidriver/lib

## install ibm_db2 drivers
RUN pecl install ibm_db2 --with-IBM_DB2=/opt/clidriver
RUN echo "extension=ibm_db2.so" >> /usr/local/etc/php/php.ini