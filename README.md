SettingsBundle
==============

Bundle is used for storing configuration with Symfony2 in database using Doctrine2 ORM.

[![Build Status](https://travis-ci.org/dmishh/SettingsBundle.png?branch=master)](https://travis-ci.org/dmishh/SettingsBundle)

## Features

* Easy-to-use
* Fast and extensible
* Per-user settings
* Settings scopes
* Settings validation using full power of Symfony2 Form Component

## Docs

* [Installation](#installation)
* [General usage](#general_usage)
* [Advanced configuration](#advanced_configuration)
* [I18n](#i18n)
* [Customization](#customization)

<a name="installation"></a>
### Installation (using Composer)

* Add the following to your `composer.json` file:

    ```js
    // composer.json
    {
        // ...
        "require": {
            // ...
            "dmishh/settings-bundle": "dev-master@dev"
        }
    }
    ```

* Update dependencies, run from command line:

    ```bash
    php composer.phar update
    ```

* Register the bundle in your ``AppKernel.php`` file:

    ```php
    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Dmishh\Bundle\SettingsBundle\SettingsBundle(),
    );
    ```

* Update your database for creating settings table:

    * Via [DoctrineMigrationsBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html):

    ```bash
    php app/console doctrine:migrations:diff
    php app/console doctrine:migrations:migrate
    ```

    * Manually:

    ```bash
    php app/console doctrine:schema:update --force
    ```

* Add following lines to your _app/config/routing.yml_ (how to override default routing and controller):

    ```yaml
    settings:
        resource: "@DmishhSettingsBundle/Resources/config/routing.yml"
        prefix: /settings
    ```

* Configure first setting, add to _app/config/config.yml_:

    ```yaml
    dmishh_settings:
        settings:
            my_first_setting: ~
    ```

* Open <strong>http://<em>your-project-url</em>/settings/manage</strong> and modify <em>my_first_setting</em>

<a name="general_usage"></a>
### General usage

* In controllers:

    ```php
    <?php

    // Set setting value by its name
    $this->get('settings_manager')->set('my_first_setting', 'value');

    // Get single setting value by its name
    $this->get('settings_manager')->get('my_first_setting'); // => 'value'

    // Get all settings
    $this->get('settings_manager')->all(); // => array('my_first_setting' => 'value')

    // Set settings' values from associative name-value array
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_value'

    ```

    ```php
    <?php

    // Each of methods above has last optional $user parameter
    // that allows to get/set per-user settings

    // $user parameter implements UserInterface from Symfony Security Component
    // Your User Entity must implement it if you wish to use per-user settings
    $this->get('settings_manager')->set('my_first_setting', 'user_value', $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'user_value'
    $this->get('settings_manager')->all($this->getUser()); //  array('my_first_setting' => 'user_value')
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_user_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_user_value'

    ```

* In services: you must inject <em>@settings_manager</em> or the whole <em>@service_container</em> into your service and use it like in the example above

* In Twig templates:

    ```twig
    {# Global setting #}
    {{ get_setting('some_setting') }} {# => 'value' #}

    {# User setting #}
    {{ get_setting('some_user_setting', app.user) }} {# => 'value' #}

    {# Getting all global settings #}
    {% for setting in get_all_settings() %}
        {{ setting }} {# => 'value', ... #}
    {% endfor %}
    ```

<a name="advanced_configuration"></a>
### Advanced configuration

Full list of options:

```yaml
dmishh_settings:
    user_class: Dmishh/Bundle/SettingsBundle/Entity/User
    layout: DmishhSettingsBundle::layout.html.twig
    template: DmishhSettingsBundle:Settings:manage.html.twig
    security:
         manage_global_settings_role: ROLE_USER
         users_can_manage_own_settings: true
    settings:
        my_first_setting:
            validation:
                type: text
                options:
                    required: false
```

<a name="validation"></a>
#### Settings validation

Settings validation uses [Symfony Forms Component](http://symfony.com/doc/current/book/forms.html#built-in-field-types).
You just specify, for example, type *[text](http://symfony.com/doc/current/reference/forms/types/text.html)* and use it's options like *max_length*, etc.

```yaml
dmishh_settings:
    settings:
        my_first_setting:
            validation:
                type: text
                options:
                    max_length: 15
```

__Note:__ [validation](#validation) is provided only at the form level.

#### Understanding scopes

Bundle provides settings separation into 3 scopes: ALL, GLOBAL and USER. GLOBAL and USER are totally independent.
ALL scope provides you to inherit global settings when user setting with same name is not setted.
Examples must give more clearance:

```php
<?php

// Example with ALL scope
$this->get('settings_manager')->set('all_scope_setting', 'value');
$this->get('settings_manager')->get('all_scope_setting'); // => 'value'
$this->get('settings_manager')->get('all_scope_setting', $this->getUser()); // => 'value'

// Example #1 with GLOBAL and USER scopes
$this->get('settings_manager')->set('global_scope_setting', 'value');
$this->get('settings_manager')->get('global_scope_setting'); // => 'value'
$this->get('settings_manager')->get('global_scope_setting', $this->getUser()); // => WrongScopeException
$this->get('settings_manager')->set('global_scope_setting', 'value', $this->getUser()); // => WrongScopeException

// Example #2 with GLOBAL and USER scopes
$this->get('settings_manager')->set('user_scope_setting', 'value', $this->getUser());
$this->get('settings_manager')->get('user_scope_setting', $this->getUser()); // => 'value'
$this->get('settings_manager')->get('user_scope_setting'); // => WrongScopeException
$this->get('settings_manager')->set('user_scope_setting', 'value'); // => WrongScopeException
```

#### Configuring per-user settings


#### Security


<a name="i18n"></a>
### I18n

#### Define custom settings names

1. Create _yml_ or _xliff_ file for domain _settings_ (example: _settings.en.yml_) in any of your bundles or directly in _app/Resources_ (note: your bundle must be activated after _DmishhSettingsBundle_ in _AppKernel.php_)
1. Add your settings translations like in the following example for _yml_ format:

```yaml
labels:
    my_custom_setting: My Custom Label
    profile_update_interval: Profile update interval
```

#### Provide translations for choice type

1. Create, if not yet, _yml_ or _xliff_ file for domain _settings_ (example: _settings.en.yml_) in any of your bundles or directly in _app/Resources_ (note: your bundle must be activated after _DmishhSettingsBundle_ in _AppKernel.php_)
1. Add your choices translations like in the following example for _yml_ format (add <i>_choice</i> postfix to your setting's name):

```yaml
labels:
    gender: Gender
    gender_choices:
        m: Male
        f: Female
```

<a name="customization"></a>
### Customization

#### Overriding layout

#### Overriding template

#### Overriding controller

<!--
### FAQ
* How to change settings table name
* How to remove prefix "dmishh_" from service names
-->

## Roadmap

#### 1.0
* First stable version

## License

The MIT License (MIT), for details, please, see [LICENSE](https://github.com/dmishh/SettingsBundle/blob/master/LICENSE)

© 2013-2014 [Dmitriy Scherbina](http://dmishh.com)
