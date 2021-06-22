SettingsBundle
==============

Bundle for storing configuration with Symfony in database using Doctrine2 ORM.

[![Build Status](https://travis-ci.org/dmishh/SettingsBundle.png?branch=master)](https://travis-ci.org/dmishh/SettingsBundle)

## Features

* Easy-to-use (Twig extension, container service)
* Settings scopes per user, global or all
* Settings validation by using the Symfony Form Component
* 2 serialization mechanisms: PHP `serialize()` and JSON (+ you can write your own)
* Settings caching (PSR-6)
* Fast and extensible

## Quick usage examples

Symfony controller:

```php
// Global settings
$settingsManager->set('name', 'foo');
$settingsManager->get('name'); // returns 'foo'

// User settings
$settingsManager->get('name', $user); // returns global 'foo'
$settingsManager->set('name', 'bar', $user);
$settingsManager->get('name', $user); // returns 'bar'
```

Twig template:

```twig
{# Global setting #}
{{ get_setting('some_setting') }} {# => 'value' #}

{# User setting #}
{{ get_setting('some_user_setting', app.user) }} {# => 'value' #}
```

See the [general usage](/Resources/doc/general-usage.md) documentation for more examples.

## Documentation

* [Installation](/Resources/doc/installation.md)
* [General usage](/Resources/doc/general-usage.md)
* [Scopes](/Resources/doc/scopes.md)
* [Advanced configuration](/Resources/doc/advanced-configuration.md)
* [I18n](/Resources/doc/i18n.md)
* [Customization](/Resources/doc/customization.md)
* [FAQ](/Resources/doc/faq.md)

## Changelog, Roadmap and contribution

Please, do not hesitate to [report bugs](https://github.com/dmishh/SettingsBundle/issues) or send
[pull requests](https://github.com/dmishh/SettingsBundle/pulls). It will help to motivate me to support
library better than anything else :)

See [CHANGELOG.md](CHANGELOG.md) for all major changes.

### Upgrade from 1.0.*

Make sure to read the [UPGRADE.md](UPGRADE.md) to successfully migrate your application.

## License

The MIT License. For the full text of license, please, see [LICENSE](/LICENSE)
