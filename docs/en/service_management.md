# Service Management

Service management is split in two different parts:

1. Service / script embedding
2. Listing of a service in the Modal

This gives you the freedom to define these two things independently from each
other (add multiple scripts with the same identifier or add a script to the
template directly and just define it in klaro).

## Require Files or Scripts
We built silverstripe-klaro to match the pattern of the
[regular silverstripe requirement](https://docs.silverstripe.org/en/4/developer_guides/templates/requirements/)
process. In order to add scripts to a page, you can use the following
functions:
```php
use Syntro\SilverstripeKlaro\KlaroRequirements;

// Add a javascript file
KlaroRequirements::klaroJavascript('path/to/file.js', 'myservice');
// Add a css file
KlaroRequirements::klaroCss('path/to/file.css', 'myservice');
// Add a custom script
KlaroRequirements::customKlaroScript(<<<JS alert('hello') JS, 'myservice');
```
The API is very similar to the original [`javascript()`, `css()` and `customScript()`](https://docs.silverstripe.org/en/4/developer_guides/templates/requirements/#php-requirements-api)
with the only real difference being the addition of the second argument which
states the name (or id) of the service which klaro uses to enable it.

## Define Services and Purposes
Klaro works by using a set of services and grouping those via purposes. You
can add new services and purposes by adding them via yaml config:
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
Each service can be configured using the options available:
- `title`* : (string) the title of the service
- `purposes`* : (string[]) purposes this service is listed under
- `description` : (string) the description of the service
- `default` : (boolean) specifying the state in the selection modal
- `cookies` : (string[]|array[]) cookies used by the service, see https://kiprotect.com/docs/klaro/annotated-configuration
- `required` : (boolean) specify if this service is required
- `optOut` : (boolean) make this service opt out
- `onlyOnce` : (boolean) load this service only once

Each Purpose can be configured with a `title` and a `description`.

### Cookies
Klaro can manage cookies set by other scripts (removing present cookies). For this,
you can specify the `cookies` key in the service configuration. you can specify
strings or an array of 3 strings (read the [klaro docs](https://kiprotect.com/docs/klaro/) for info):
```yaml
Syntro\SilverstripeKlaro\Config:
    klaro_services:
        myservice:
            title: My Service
            purposes: [ 'mypurpose' ]
            cookies:
                - _cookie
                - [ 'cookie', '/', 'localhost']
```
> Currently, regex are not working!

## Configure klaro
Global options are available via the `klaro_options` config option:
```yaml
Syntro\SilverstripeKlaro\Config:
    klaro_options:
        default: true
        mustConsent: true
```
Available options are:
- `testing`
- `elementID`
- `storageMethod`
- `storageName`
- `htmlTexts` (default: true)
- `cookieDomain`
- `cookieExpiresAfterDays`
- `default`
- `mustConsent`
- `acceptAll`
- `hideDeclineAll`
- `hideLearnMore`
