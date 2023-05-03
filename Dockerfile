FROM tripalproject/tripaldocker:latest

COPY . /var/www/drupal9/web/modules/contrib/trpdownload_api

## Enable module
WORKDIR /var/www/drupal9/web/modules/contrib/trpdownload_api
RUN service postgresql restart \
  && drush en trpdownload_api --yes
