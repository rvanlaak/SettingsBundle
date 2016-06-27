## SettingsBundle

* **Installation**
* [General usage](general-usage.md)
* [Scopes](scopes.md)
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* [Customization](customization.md)
* [FAQ](faq.md)

## Installation (using Composer)

* Add the following to your `composer.json` file:

    ```js
    // composer.json
    {
        "require": {
            // ...
            "dmishh/settings-bundle": "2.0.*@dev"
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
        new Dmishh\SettingsBundle\DmishhSettingsBundle(),
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

* Add following lines to your _app/config/routing.yml_ (see [how to override default routing and controller](#overriding_controller)):

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

* Open <a href="http://YOUR-PROJECT-URL/app_dev.php/settings/global">http://YOUR-PROJECT-URL/app_dev.php/settings/global</a> and start managing your settings!

**Note:** If you're using Symfony 3, please see the instructions in [Advanced configuration](advanced-configuration.md).

