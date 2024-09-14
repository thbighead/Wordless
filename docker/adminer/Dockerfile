FROM adminer:4

LABEL maintainer="Patrick Artounian <partounian@gmail.com>"

# Add volume for sessions to allow session persistence
VOLUME /sessions

#####################################
# SQL SERVER:
#####################################
USER root

ARG INSTALL_MSSQL=false
ENV INSTALL_MSSQL=${INSTALL_MSSQL}

RUN if [ ${INSTALL_MSSQL} = true ]; then \
  set -xe \
  && apk update \
  && apk add --no-cache --virtual .php-build-dependencies unixodbc-dev freetds-dev \
  && apk add --virtual .php-runtime-dependencies unixodbc freetds \
  && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
  && docker-php-ext-install pdo_odbc pdo_dblib \
  && apk del .php-build-dependencies \
  && rm -rf /var/cache/apk/* \
;fi

RUN rm /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini \
&& rm /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini

RUN	echo "upload_max_filesize = 500M" >> /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini \
&&	echo "post_max_size = 500M" >> /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini \
&&	echo "memory_limit = 1G" >> /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini \
&&	echo "max_execution_time = 0" >> /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini \
&&	echo "max_input_vars = 5000" >> /etc/php/7.4/fpm/conf.d/0-upload_large_dumps.ini

RUN	echo "upload_max_filesize = 500M" >> /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini \
&&	echo "post_max_size = 500M" >> /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini \
&&	echo "memory_limit = 1G" >> /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini \
&&	echo "max_execution_time = 0" >> /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini \
&&	echo "max_input_vars = 5000" >> /etc/php/7.4/cli/conf.d/0-upload_large_dumps.ini

USER adminer

# We expose Adminer on port 8080 (Adminer's default)
EXPOSE 8080
