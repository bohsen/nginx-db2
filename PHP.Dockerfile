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
RUN pecl install ibm_db2 --with-IBM_DB2=/opt/clidriver pdo_db2 --with-pdo-ibm=/opt/clidriver
RUN echo "extension=ibm_db2.so" > /usr/local/etc/php/conf.d/ibm_db2.ini
RUN echo "extension=pdo_ibm.so" >> /usr/local/etc/php/conf.d/ibm_db2.ini
RUN echo "extension=pdo.so" >> /usr/local/etc/php/conf.d/ibm_db2.ini

RUN echo "extension=ibm_db2.so" >> /usr/local/etc/php/php.ini
RUN echo "extension=pdo.so" >> /usr/local/etc/php/php.ini
RUN echo "extension=pdo_ibm.so" >> /usr/local/etc/php/php.ini
RUN echo "[ibm_db2]" >> /usr/local/etc/php/php.ini
RUN echo "ibm_db2.instance_name=db2inst1" >> /usr/local/etc/php/php.ini
RUN echo "[PDO_IBM instance name]" >> /usr/local/etc/php/php.ini
RUN echo "pdo_ibm.db2_instance_name=db2inst1" >> /usr/local/etc/php/php.ini