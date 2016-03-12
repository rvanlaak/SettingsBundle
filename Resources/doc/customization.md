## SettingsBundle

* [Installation](installation.md)
* [General usage](general-usage.md)
* [Scopes](scopes.md)
* [Advanced configuration](advanced-configuration.md)
* [I18n](i18n.md)
* **Customization**
* [FAQ](faq.md)

## Customization

#### Overriding layout via bundle inheritance

Make use of Symfony Bundle Inheritance to expose the bundle via your own template. See the Symfony Cookbook for an
article about how to override parts of a bundle.

* [How to Use Bundle Inheritance to Override Parts of a Bundle »](http://symfony.com/doc/current/cookbook/bundles/inheritance.html#overriding-resources-templates-routing-etc)

#### Overriding template

The template the bundle controller will use can be overwritten by changing the configuration parameter.

```yaml
dmishh_settings:
    template: DmishhSettingsBundle:Settings:manage.html.twig # change to your own
```

<a name="overriding_controller"></a>
#### Overriding controller

TODO
