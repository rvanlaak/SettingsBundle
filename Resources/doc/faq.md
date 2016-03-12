## SettingsBundle

* [Installation](installation.md)
* [General usage](general-usage.md)
* [Scopes](scopes.md)
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* [Customization](customization.md)
* **FAQ**

## FAQ

**? How to add optional setting?**

Add `required: false` to setting validation options

```yaml
dmishh_settings:
    settings:
        my_first_setting:
            options:
                required: false
```

**? How to add an `array` setting?**

TODO

**? How to inject `settings_manager` into form?**

TODO
