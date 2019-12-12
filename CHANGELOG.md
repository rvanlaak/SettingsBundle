## Changelog

#### Version 3.0.0-beta1

* Added support for Symfony 4 & 5
* Added FCQN as service names
* Added PHP 7 type hints
* Dropped support for Symfony < 3.4
* Dropped support for PHP < 7.2

#### Version 2.0.0-dev

* Added optional caching
* New interface for your entity. We are no longer using `UserInterface`. Use `SettingsOwnerInterface` instead.
* Changed behavior of `SettingsManager::all`. It will not return global config if the user/local values are missing
* Added possibility to add default value as third parameter on `SettingsManager::get`
* Updated namespace to `Dmishh\SettingsBundle` instead of `Dmishh\Bundle\SettingsBundle`
* Updated the configuration. This break BC but makes sure the configuration is not as "deep". [#31](https://github.com/dmishh/SettingsBundle/issues/31)
* Bump PHP to `^5.5.9` and Symfony to `^2.7|^3.0` [#50](https://github.com/dmishh/SettingsBundle/issues/50)

#### Version 1.0.2-1.0.7 (9 Mar 2015)
* Minor code improvements and bug fixes
* System messages translations to en, it, es, fr, de, ru, uk, sv languages

#### Version 1.0.1 (13 May 2014)
* Ability to choose serialization mechanism (php or json)
* Ability to add constraints to validation

#### Version 1.0.0 (3 Apr 2014)
* First stable version
