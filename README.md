# Tripal Download API

This module provides an API for downloading Tripal/Chado data. Since download functionality is often sought after for Tripal sites and Views Data Export is not currently meeting our needs, this module aims to provide an API to aid module and site developers in making efficient, user friendly downloads available.

**Note: This branch is to provide support for Tripal 4.x and Drupal 9.4+**

While we are starting work here, this service may find it's way into Tripal core.

## Development

```
git clone https://github.com/tripal/trpdownload_api.git
cd trpdownload_api
git checkout 2.x
docker build --tag=tripal/trpdownload_api:latest .
docker run --publish=80:80 -tid --volume=`pwd`:/var/www/drupal9/web/modules/contrib/trpdownload_api --name=trpdownload tripal/trpdownload_api:latest
docker exec trpdownload service postgresql restart
```
