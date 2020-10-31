# Translations

`silverstripe-klaro` allows to translate service/purpose titles and descriptions
by using the silverstripe translation service. Each service or purpose you
specify using the [configuration api](configuration.md) will try to render
a title specified by the key `Syntro\SilverstripeKlaro\Config.service_{name}_{field}`
or `Syntro\SilverstripeKlaro\Config.purpose_{name}_{field}` respectively.


If you need to translate an entry, just create a file containing these keys.
Take a look at the german translation of the default purpose and service in
this module:
```
de:
  Syntro\SilverstripeKlaro\Config:
    purpose_required_title: Notwendige Services
    purpose_required_description: Diese Dienste sind für die Nutzung dieser Seite erforderlich.
    service_session_title: Session
    service_session_description: Dies ist das Standard-Session-Cookie.
```
