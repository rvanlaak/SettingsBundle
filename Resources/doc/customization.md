## SettingsBundle

* [Installation](installation.md)
* [General usage](general-usage.md)
* [Scopes](scopes.md)
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* **Customization**
* [FAQ](faq.md)

## Customization

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
