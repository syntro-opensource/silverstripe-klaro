# Silverstripe Klaro

[![🎭 Tests](https://github.com/syntro-opensource/silverstripe-klaro/workflows/%F0%9F%8E%AD%20Tests/badge.svg)](https://github.com/syntro-opensource/silverstripe-klaro/actions?query=workflow%3A%22%F0%9F%8E%AD+Tests%22+branch%3A%22master%22)
[![codecov](https://codecov.io/gh/syntro-opensource/silverstripe-klaro/branch/master/graph/badge.svg)](https://codecov.io/gh/syntro-opensource/silverstripe-klaro)
![Dependabot](https://img.shields.io/badge/dependabot-active-brightgreen?logo=dependabot)
[![phpstan](https://img.shields.io/badge/PHPStan-enabled-success)](https://github.com/phpstan/phpstan)
[![composer](https://img.shields.io/packagist/dt/syntro/silverstripe-klaro?color=success&logo=composer)](https://packagist.org/packages/syntro/silverstripe-klaro)
[![Packagist Version](https://img.shields.io/packagist/v/syntro/silverstripe-klaro?label=stable&logo=composer)](https://packagist.org/packages/syntro/silverstripe-klaro)

Silverstripe module for implementing a [GDPR](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) compliant service notice on a silverstripe based website.

## Introduction
from the [klaro](https://kiprotect.com/klaro) website:

> Klaro is a simple and powerful open source consent management platform (CMP) that helps you to meet the requirements of the GDPR and to protect the privacy of your website visitors and users.

Simply put, klaro is an easy to use and easy to set up tool for GDPR compliant
cookie and service management. As Silverstripe does not provide such a mechanism
out of the box, klaro can help fill in the gap for sites and applications
aimed at european audiences.

While there are some modules around which allow the integration of klaro in a
silverstripe based project, most of them use a database-centered approach for
managing cookies and services. At Syntro, we believe that this approach, while
flexible, does not do justice to the complexity of the subject matter and,
because of the editability (and especially removability) of services by the end
user, **can be dangerous**. For this reason, we introduced our own module.

`syntro/silverstripe-klaro` uses the
[silverstripe configuration api](https://docs.silverstripe.org/en/4/developer_guides/configuration/configuration/)
and the [silverstripe requirements pattern](https://docs.silverstripe.org/en/4/developer_guides/templates/requirements/)
to manage services in an easy to learn way while giving the end user some control
about texts displayed in the notice and modal.

Give it a try, Feedback is always welcome!

## Installation
To install this module, run the following command:
```
composer require syntro/silverstripe-klaro
```

## Usage
### Getting Started
If you just want to add a new service, add the script like so:
```php
use Syntro\SilverstripeKlaro\KlaroRequirements;

// ...

KlaroRequirements::klaroJavascript('path/to/file.js', 'myservice');

```
In order to add this service to the notice, create a config like so:
```yaml
Syntro\SilverstripeKlaro\Config:
    klaro_purposes:
        mypurpose:
            title: My Purpose
            description: This is my purpose
    klaro_services:
        myservice:
            title: My Service
            purposes: [ 'mypurpose' ]
```
After flushing, the modal will display `myservice` and the script will only be
active if accepted by the user.

### Docs
Please read the documentation for further information about how to customise klaro:
1. [Service management](docs/en/service_management.md)
2. [Translations](docs/en/translations.md)
3. [Styling](docs/en/styling.md)

## Contributing
See [CONTRIBUTION.md](CONTRIBUTION.md) for mor info.
