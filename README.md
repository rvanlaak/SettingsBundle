SettingsBundle
==============

Bundle is used for storing configuration with Symfony2 in database using Doctrine2 ORM.

**Bundle is under heavy development, please, be patient :)**

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
* Retreiving settings in Twig
* I18n
* Custom security
* Custom templates

<a name="installation"></a>
### Installation (using Composer)

* Add the following to your `composer.json` file:

    ```js
    // composer.json
    {
        // ...
        "require": {
            // ...
            "dmishh/settings-bundle": "dev-master"
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
        new Dmishh\Bundle\SettingsBundle\SettingsBundle()
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
        resource: "@DmishhSettingsBundle/Controller/SettingsController.php"
        type: annotation
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

    // Sets setting value by its name
    $this->get('settings_manager')->set('my_first_setting', 'value');

    // Returns single setting value by its name
    $this->get('settings_manager')->get('my_first_setting'); // => 'value'

    // Returns all settings
    $this->get('settings_manager')->all(); // => array('my_first_setting' => 'value')

    // Sets settings values from associative name-value array
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_value'


    // Each of this methods has last optional $user parameter
    // that allows to get/set per-user settings

    // $user parameter implements UserInterface and your User Entity
    // must implement it if you wish to use per-user settings
    $this->get('settings_manager')->set('my_first_setting', 'user_value', $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'user_value'
    $this->get('settings_manager')->all($this->getUser()); //  array('my_first_setting' => 'user_value')
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_user_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_user_value'

    ```

* In services/etc: you must inject <em>@settings_manager</em> or the whole <em>@service_container</em> into your service and use it like in the example above

* In Twig templates:

    ```twig
    {% if settings_manager_get('') %}
        OK!
    {% endif %}

    {% for setting in settings_manager_all() %}
        {{ setting }}
    {% endfor %}
    ```

__Note:__ validation is provided only at the form level.

<a name="advanced_configuration"></a>
### Advanced configuration

UserClass
Security
Scopes

#### Usage in templates

#### I18n

<!--
### FAQ
* How to change settings table name
* How to remove prefix "dmishh_" from service names
-->

### Roadmap

#### 1.*
* Add some default themes (like bootstrap)
* Add DocumentManager support

#### 1.0
* First stable version

### License

The MIT License (MIT), for details, please, see [LICENSE](https://github.com/dmishh/SettingsBundle/blob/master/LICENSE)

© 2013 Dmitriy Scherbina <http://dmishh.com>
