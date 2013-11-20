SettingsBundle
==============

[![Build Status](https://travis-ci.org/dmishh/SettingsBundle.png?branch=master)](https://travis-ci.org/dmishh/SettingsBundle)

### About

Bundle is used for storing configuration with Symfony2 in database using Doctrine2 ORM.

### Features

* Easy-to-use
* Fast and extensible
* Per-user settings
* Settings scopes
* Settings validation using Symfony2 Form Component

### Manual

* Installation
* Configuration
* General usage
* Retreiving settings in Twig
* I18n
* Custom security
* Custom templates

#### Installation (using Composer)

1. Add the following to your `composer.json` file:

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

1. Update dependencies, run from command line:

    ```bash
    php composer.phar update
    ```

1. Register the bundle in your ``AppKernel.php`` file:

    ```php
    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Dmishh\Bundle\SettingsBundle\SettingsBundle()
    );
    ```

#### Configuration

Security
Scopes

#### General usage

#### Usage in templates

#### I18n

<!--
### FAQ
* How to change settings table name
* How to remove prefix "dmishh_" from service names
-->

### Roadmap

#### 1.1
* Add some default themes (like bootstrap)
* Add PHP templates support

#### 1.0
* First stable version

### License

The MIT License (MIT), for details, please, see [LICENSE](https://github.com/dmishh/SettingsBundle/blob/master/LICENSE)

© 2013 Dmitriy Scherbina <http://dmishh.com>
