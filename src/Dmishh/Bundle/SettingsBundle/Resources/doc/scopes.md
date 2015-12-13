## SettingsBundle

* [Installation](installation.md)
* [General usage](general-usage.md)
* **Scopes**
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* [Customization](customization.md)
* [FAQ](faq.md)

## Understanding scopes

Bundle provides settings separation into 3 scopes: `ALL`, `GLOBAL` and `USER`.

* GLOBAL and USER scopes are totally independent.
* ALL scope provides you to inherit global settings when user setting with the same name is not setted.

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
