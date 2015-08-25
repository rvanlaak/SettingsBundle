SettingsBundle
==============

Bundle is used for storing configuration with Symfony2 in database using Doctrine2 ORM.

[![Build Status](https://travis-ci.org/dmishh/SettingsBundle.png?branch=master)](https://travis-ci.org/dmishh/SettingsBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5375684f-5b40-489a-aca5-eb01c3ca5ac2/small.png)](https://insight.sensiolabs.com/projects/5375684f-5b40-489a-aca5-eb01c3ca5ac2)

Thanks to **[Tobias Nyholm](https://github.com/Nyholm)** and [Artem Zhuravlov](https://github.com/azhuravlov) for contribution.

## Features

* Easy-to-use (Twig extension, container service)
* Fast and extensible
* Settings scopes per user, global or all
* Settings validation using full power of Symfony2 Form Component
* 2 serialization mechanisms in DB: PHP's native `serialize()` and JSON + you can write your own
* Settings caching

## Quick usage examples

Symfony controller:

```php
// Global settings
$this->get('settings_manager')->set('name', 'foo');
$this->get('settings_manager')->get('name'); // returns 'foo'

// User settings
$this->get('settings_manager')->get('name', $user); // returns global 'foo'
$this->get('settings_manager')->set('name', 'bar', $user); 
$this->get('settings_manager')->get('name', $user); // returns 'bar'
```

Twig template: 

```twig
{# Global setting #}
{{ get_setting('some_setting') }} {# => 'value' #}

{# User setting #}
{{ get_setting('some_user_setting', app.user) }} {# => 'value' #}
```
    
See the [general usage](/src/Dmishh/Bundle/SettingsBundle/Resources/doc/usage.md) documentation for more examples.

## Documentation

* [Installation](src/Dmishh/Bundle/SettingsBundle/Resources/doc/installation.md)
* [General usage](src/Dmishh/Bundle/SettingsBundle/Resources/doc/usage.md)
* [Scopes](src/Dmishh/Bundle/SettingsBundle/Resources/doc/scope.md)
* [Advanced configuration](src/Dmishh/Bundle/SettingsBundle/Resources/doc/configuration.md)
* [I18n](src/Dmishh/Bundle/SettingsBundle/Resources/doc/i18n.md)
* [Customization](src/Dmishh/Bundle/SettingsBundle/Resources/doc/customization.md)
* [FAQ](src/Dmishh/Bundle/SettingsBundle/Resources/doc/faq.md)

## Roadmap and contribution

Please, do not hesitate to [report bugs](https://github.com/dmishh/SettingsBundle/issues) or send 
[pull requests](https://github.com/dmishh/SettingsBundle/pulls). It will help to motivate me to support 
library better than anything else :)

#### Version 2.0.0-dev

* Added optional caching
* New interface for your entity. We are no longer using `UserInterface`. Use `SettingsOwnerInterface` instead.
* Changed behavior of `SettingsManager::all`. It will not return global config if the user/local values are missing
* Added possibility to add default value as third parameter on `SettingsManager::get`

#### Version 1.0.2-1.0.7
* Minor code improvements and bug fixes
* System messages translations to en, it, es, fr, de, ru, uk, sv languages

#### Version 1.0.1
* Ability to choose serialization mechanism (php or json)
* Ability to add constraints to validation

#### Version 1.0.0
* First stable version

### Upgrade from 1.0.*

Make sure to read the [UPGRADE.md](UPGRADE.md) to successfully migrate your application.

## License

The MIT License. For the full text of license, please, see [LICENSE](https://github.com/dmishh/SettingsBundle/blob/master/LICENSE)

© 2013-2015 [Dmitriy Scherbina](http://dmishh.com)
