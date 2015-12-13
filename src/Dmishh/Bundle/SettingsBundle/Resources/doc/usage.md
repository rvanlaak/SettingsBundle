### General usage

* In controllers:

    ```php
    <?php

    // GLOBAL SETTINGS

    // Set setting value by its name
    $this->get('settings_manager')->set('my_first_setting', 'value');

    // Get setting value by its name
    $this->get('settings_manager')->get('my_first_setting'); // => 'value'

    // Get all settings
    $this->get('settings_manager')->all(); // => array('my_first_setting' => 'value')

    // Set settings' values from associative name-value array
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_value'));
    $this->get('settings_manager')->get('my_first_setting'); // => 'new_value'

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
    $this->get('settings_manager')->set('my_first_setting', 'user_value', $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'user_value'
    $this->get('settings_manager')->all($this->getUser()); //  array('my_first_setting' => 'user_value')
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_user_value'), $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'new_user_value'


    // PER ENTITY SETTINGS

    // This is the most interesting part. You can have settings for any entity.
    // Just make sure you have unique values for getSettingIdentifier()

    // class Company implements SettingsOwnerInterface {
    //     public function getSettingIdentifier() {
    //         return 'company_' . $this->id;
    //     }
    // }

    $myCompany = new Company();
    $this->get('settings_manager')->set('delivery_frequency_setting', 'daily', $myCompany);
    $this->get('settings_manager')->get('delivery_frequency_setting', $this->getUser()); // => 'daily'
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
