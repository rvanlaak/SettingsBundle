## SettingsBundle

* [Installation](installation.md)
* **General usage**
* [Scopes](scopes.md)
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* [Customization](customization.md)
* [FAQ](faq.md)

## General usage

* In controllers:

    ```php
    <?php

    // GLOBAL SETTINGS

    // Set setting value by its name
    $settingsManager->set('my_first_setting', 'value');

    // Get setting value by its name
    $settingsManager->get('my_first_setting'); // => 'value'

    // Get all settings
    $settingsManager->all(); // => array('my_first_setting' => 'value')

    // Set settings' values from associative name-value array
    $settingsManager->setMany(array('my_first_setting' => 'new_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_value'$settingsManager

    ```

    ```php
    <?php

    // PER USER SETTINGS

    // Each of methods above has last optional $user parameter
    // that allows to get/set per-user settings
    // Your User Entity must implement SettingsOwnerInterface if you wish to use per-user settings

    // class User implements SettingsOwnerInterface {
    //     public function getSettingIdentifier() {
    //         return $this->id;
    //     }
    // }

    // These are same examples as above with only difference that they are for current user
    $settingsManager->set('my_first_setting', 'user_value', $this->getUser());
    $settingsManager->get('my_first_setting', $this->getUser()); // => 'user_value'
    $settingsManager->all($this->getUser()); //  array('my_first_setting' => 'user_value')
    $settingsManager->setMany(array('my_first_setting' => 'new_user_value'), $this->getUser());
    $settingsManager->get('my_first_setting', $this->getUser()); // => 'new_user_value'


    // PER ENTITY SETTINGS

    // This is the most interesting part. You can have settings for any entity.
    // Just make sure you have unique values for getSettingIdentifier()

    // class Company implements SettingsOwnerInterface {
    //     public function getSettingIdentifier() {
    //         return 'company_' . $this->id;
    //     }
    // }

    $myCompany = new Company();
    $settingsManager->set('delivery_frequency_setting', 'daily', $myCompany);
    $settingsManager->get('delivery_frequency_setting', $this->getUser()); // => 'daily'
    ```

* In services: you must inject <em>@settings_manager</em> or the whole <em>@service_container</em> into your service and use it in the same way as in controllers (like in the example above)

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
