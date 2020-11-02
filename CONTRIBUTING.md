# Contributing to this module

If you would like to contribute to this module please open a pull request on Github.

## Setup
We use [ssdev](https://github.com/syntro-opensource/ssdev) to develop modules
and project. If you want to get started with it, follow these steps to work
on this module:

- install dependencies for [ssdev](https://github.com/syntro-opensource/ssdev)
- run `npx ssdev init path/to/moduleproject` and change to the root dir.
- run `npx ssdev serve`
- install this module with `npx ssdev run composer -- require syntro/silverstripe-klaro:dev-master`
- make your changes
- run the tests (in the root directory where you have inited)
  - PHPUnit: `npx ssdev run -- vendor/bin/phpunit vendor/syntro/silverstripe-klaro/`
  - PHPCS: `npx ssdev run -- vendor/bin/phpcs vendor/syntro/silverstripe-klaro/`
  - PHPStan: `ssdev run -- bash -c "cd vendor/syntro/silverstripe-klaro && ../../bin/phpstan analyse src/ --memory-limit=1G -c phpstan-dev.neon -a ../../symbiote/silverstripe-phpstan/bootstrap.php --no-ansi --level 4"`
- open a pull request to the `master` branch
