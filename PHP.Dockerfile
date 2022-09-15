FROM php:fpm

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Documentation
# https://stackoverflow.com/questions/37066985/php-connection-to-db2
# https://github.com/nagstaku/php_db2
# https://github.com/php/pecl-database-ibm_db2
# https://github.com/php/pecl-database-pdo_ibm

# Install DB2 php-extensions

RUN mkdir -p /opt/ibm/ && curl https://public.dhe.ibm.com/ibmdl/export/pub/software/data/db2/drivers/odbc_cli/linuxx64_odbc_cli.tar.gz | tar -xz -C /opt/ibm/
# if you prefer to keep the file locally, download it and use:
# ADD odbc_cli/linuxx64_odbc_cli.tar.gz /opt/ibm/
# ENTRYPOINT [ "/opt/ibm/installDSDriver" ] 

## set env vars needed for PECL install
ENV IBM_DB_HOME=/opt/ibm/clidriver
ENV LD_LIBRARY_PATH=/opt/ibm/clidriver/lib

## install ibm_db2 drivers
RUN pecl install ibm_db2 pdo_ibm
RUN echo "extension=ibm_db2.so" > /usr/local/etc/php/conf.d/ibm_db2.ini
RUN echo "extension=pdo.so" > /usr/local/etc/php/php.ini
RUN echo "extension=pdo_ibm.so" > /usr/local/etc/php/php.ini