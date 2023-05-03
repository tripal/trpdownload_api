# Tripal Download API

This module provides an API for downloading Tripal/Chado data. Since download functionality is often sought after for Tripal sites and Views Data Export is not currently meeting our needs, this module aims to provide an API to aid module and site developers in making efficient, user friendly downloads available.

**Note: This branch is to provide support for Tripal 4.x and Drupal 9.4+**

While we are starting work here, this service may find it's way into Tripal core.

## Automated Testing

This package is dedicated to a high standard of automated testing. We use
PHPUnit for testing and CodeClimate to ensure good test coverage and maintainability.
There are more details on [our CodeClimate project page] describing our specific
maintainability issues and test coverage.

![MaintainabilityBadge]
![TestCoverageBadge]

The following compatibility is proven via automated testing workflows.

| Drupal | 9.3.x | 9.4.x | 9.5.x | 10.0.x |
|--------|-------|-------|-------|--------|
| **PHP 8.0** | ![Grid1A-Badge] | ![Grid1B-Badge] | ![Grid1C-Badge] |  |
| **PHP 8.1** | ![Grid2A-Badge] | ![Grid2B-Badge] | ![Grid2C-Badge] |  |

[our CodeClimate project page]: https://github.com/tripal/trpdownload_api
[MaintainabilityBadge]: https://api.codeclimate.com/v1/badges/3c768190d8546a51075e/maintainability
[TestCoverageBadge]: https://api.codeclimate.com/v1/badges/3c768190d8546a51075e/test_coverage

[Grid1A-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid1A.yml/badge.svg
[Grid1B-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid1B.yml/badge.svg
[Grid1C-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid1C.yml/badge.svg

[Grid2A-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid2A.yml/badge.svg
[Grid2B-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid2B.yml/badge.svg
[Grid2C-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid2C.yml/badge.svg
[Grid2D-Badge]: https://github.com/tripal/trpdownload_api/actions/workflows/MAIN-phpunit-Grid2D.yml/badge.svg

## Development

```
git clone https://github.com/tripal/trpdownload_api.git
cd trpdownload_api
git checkout 2.x
docker build --tag=tripal/trpdownload_api:latest .
docker run --publish=80:80 -tid --volume=`pwd`:/var/www/drupal9/web/modules/contrib/trpdownload_api --name=trpdownload tripal/trpdownload_api:latest
docker exec trpdownload service postgresql restart
```
