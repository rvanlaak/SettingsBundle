SettingsBundle
==============

Bundle is used for storing configuration with Symfony2 in database using Doctrine2 ORM.

[![Build Status](https://travis-ci.org/dmishh/SettingsBundle.png?branch=master)](https://travis-ci.org/dmishh/SettingsBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5375684f-5b40-489a-aca5-eb01c3ca5ac2/small.png)](https://insight.sensiolabs.com/projects/5375684f-5b40-489a-aca5-eb01c3ca5ac2)

Thanks to [Tobias Nyholm](https://github.com/Nyholm) and [Artem Zhuravlov](https://github.com/azhuravlov) for contribution.

## Features

* Easy-to-use
* Fast and extensible
* Per-user settings
* Settings scopes
* Settings validation using full power of Symfony2 Form Component + built-in or custom constraints
* 2 serialization mechanisms in DB: PHP's native `serialize()` and JSON + you can write your own

## Docs

* [Installation](#installation)
* [General usage](#general_usage)
* [Advanced configuration](#advanced_configuration)
* [I18n](#i18n)
* [Customization](#customization)
* [FAQ](#faq)

<a name="installation"></a>
### Installation (using Composer)

* Add the following to your `composer.json` file:

    ```js
    // composer.json
    {
        "require": {
            // ...
            "dmishh/settings-bundle": "1.0.*"
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
        new Dmishh\Bundle\SettingsBundle\DmishhSettingsBundle(),
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

<a name="general_usage"></a>
### General usage

* In controllers:

    ```php
    <?php

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

    // Each of methods above has last optional $user parameter
    // that allows to get/set per-user settings
    // $user parameter implements UserInterface from Symfony Security Component
    // Your User Entity must implement UserInterface if you wish to use per-user settings

    // These are same examples as above with only difference that they are for current user
    $this->get('settings_manager')->set('my_first_setting', 'user_value', $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'user_value'
    $this->get('settings_manager')->all($this->getUser()); //  array('my_first_setting' => 'user_value')
    $this->get('settings_manager')->setMany(array('my_first_setting' => 'new_user_value'), $this->getUser());
    $this->get('settings_manager')->get('my_first_setting', $this->getUser()); // => 'new_user_value'

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

<a name="advanced_configuration"></a>
### Advanced configuration

You can configure most of bundle behavior and even more — override everything on your own :)

Full list of options:

```yaml
dmishh_settings:
    layout: DmishhSettingsBundle::layout.html.twig
    template: DmishhSettingsBundle:Settings:manage.html.twig
    security:
         manage_global_settings_role: ROLE_USER
         users_can_manage_own_settings: true
    serialization: php # database serialization mechanism (php|json)
    settings:
        my_first_setting:
            validation:
                type: number # any Symfony2 form type
                options: # options passed to form
                    required: false
                    constraints:
                        Symfony\Component\Validator\Constraints\Range:
                            min: 1
                            max: 65535
```

<a name="validation"></a>
#### Settings validation

Settings validation uses [Symfony Forms Component](http://symfony.com/doc/current/book/forms.html#built-in-field-types).
You just specify, for example, type *[text](http://symfony.com/doc/current/reference/forms/types/text.html)* and use it's options like *max_length*, etc.
Also you can use [built-in](http://symfony.com/doc/current/reference/constraints.html) or [custom constraints](http://symfony.com/doc/current/cookbook/validation/custom_constraint.html).

```yaml
dmishh_settings:
    settings:
        my_first_setting:
            validation:
                type: text
                options:
                    max_length: 15
                    constraints:
                        Symfony\Component\Validator\Constraints\Regex:
                            pattern: "/^\d+$/"
```

__Note:__ [validation](#validation) is provided only at the form level.

#### Understanding scopes

Bundle provides settings separation into 3 scopes: ALL, GLOBAL and USER.

GLOBAL and USER scopes are totally independent.
ALL scope provides you to inherit global settings when user setting with the same name is not setted.
Examples must give more clearance:

```php
<?php

// Example with ALL scope
$this->get('settings_manager')->set('all_scope_setting', 'value');
$this->get('settings_manager')->get('all_scope_setting'); // => 'value'
$this->get('settings_manager')->get('all_scope_setting', $this->getUser()); // => 'value'
$this->get('settings_manager')->set('all_scope_setting', 'user_value', $this->getUser());
$this->get('settings_manager')->get('all_scope_setting', $this->getUser()); // => 'user_value'

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

#### Configuring scope

You may configure a scope to each of your settings. You can use ALL (default), GLOBAL or USER scope.

```yaml
dmishh_settings:
    settings:
        my_first_user_setting:
            scope: user # all, global
```

#### Security

To protect settings modification bundle uses Symfony Security Component.
You can limit global settings modification with ```manage_global_settings_role``` and grant access to authenticated users to modify their settings.

```yaml
dmishh_settings:
    security:
         manage_global_settings_role: ROLE_USER
         users_can_manage_own_settings: true
```


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

Clear your cache with ```app/console cache:clear```

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

Clear your cache with ```app/console cache:clear```

<a name="customization"></a>
### Customization

#### Overriding layout

##### Via config

Set your layout in config

```yaml
dmishh_settings:
    layout: DmishhSettingsBundle::layout.html.twig # change to your own
```

Place ```settings_form``` block near your main content block

```twig
{% block settings_form %}{% endblock %}
```

##### Via bundle inheritance

TODO

#### Overriding template

```yaml
dmishh_settings:
    template: DmishhSettingsBundle:Settings:manage.html.twig # change to your own
```

<a name="overriding_controller"></a>
#### Overriding controller

TODO

<a name="faq"></a>
### FAQ

**→ How to add optional setting?**

Add `required: false` to setting validation options

```yaml
dmishh_settings:
    settings:
        my_first_setting:
            validation:
                required: false
```

**→ How to add an `array` setting?**

TODO

**→ How to inject `settings_manager` into form?**

TODO

## Roadmap and contribution

Please, do not hesitate to [report bugs](https://github.com/dmishh/SettingsBundle/issues) or send [pull requests](https://github.com/dmishh/SettingsBundle/pulls). It will help to motivate me to support library better than anything else :)

#### Version 1.0.2-1.0.6
* Minor code improvements and bug fixes
* System messages translations to en, it, es, fr, de, ru, uk, sv languages

#### Version 1.0.1
* Ability to choose serialization mechanism (php or json)
* Ability to add constraints to validation

#### Version 1.0.0
* First stable version

## License

The MIT License. For the full text of license, please, see [LICENSE](https://github.com/dmishh/SettingsBundle/blob/master/LICENSE)

© 2013-2015 [Dmitriy Scherbina](http://dmishh.com)
