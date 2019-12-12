## SettingsBundle

* [Installation](installation.md)
* [General usage](general-usage.md)
* [Scopes](scopes.md)
* **Advanced configuration**
* [I18n](i18n.md)
* [Customization](customization.md)
* [FAQ](faq.md)

## Advanced configuration

Full list of options:

```yaml
dmishh_settings:
    template: DmishhSettingsBundle:Settings:manage.html.twig
    cache_service: null
    cache_lifetime: 3600
    security:
         manage_global_settings_role: ROLE_USER
         users_can_manage_own_settings: true
    serialization: php # database serialization mechanism (php|json)
    settings:
        my_first_setting:
            scope: all # global or user
            type: number # any Symfony form type, or FQCN for Symfony >=3.0
            options: # options passed to form
                required: false
            constraints:
                Symfony\Component\Validator\Constraints\Range:
                    min: 1
                    max: 65535
```

**Note:** In Symfony 3, use the fully qualified class name instead of the form type name. 


#### Settings validation

Settings validation uses [Symfony Forms Component](http://symfony.com/doc/current/book/forms.html#built-in-field-types).
You just specify, for example, type *[text](http://symfony.com/doc/current/reference/forms/types/text.html)* and use it's options like *max_length*, etc.
Also you can use [built-in](http://symfony.com/doc/current/reference/constraints.html) or [custom constraints](http://symfony.com/doc/current/cookbook/validation/custom_constraint.html).

```yaml
dmishh_settings:
    settings:
        my_first_setting:
            type: text
            options:
                max_length: 15
            constraints:
                Symfony\Component\Validator\Constraints\Regex:
                    pattern: "/^\d+$/"
```

__Note:__ [validation](#validation) is provided only at the form level.

#### Security

To protect settings modification bundle uses Symfony Security Component.
You can limit global settings modification with ```manage_global_settings_role``` and grant access to authenticated users to modify their settings.

```yaml
dmishh_settings:
    security:
         manage_global_settings_role: ROLE_USER
         users_can_manage_own_settings: true
```

#### Caching

If you want to cache your settings you may provide a cache service that implements `Psr\Cache\CacheItemPoolInterface`.
Every time you fetch a setting from the database we will cache it for `cache_lifetime` seconds. If you edit the
setting we will automatically invalidate the cache.

```yaml
dmishh_settings:
    cache_service: cache.provider.my_redis
    cache_lifetime: 3600

# Using cache/adapter-bundle
cache_adapter:
    providers:
        my_redis:
            factory: 'cache.factory.redis'
```

Read more about how you configure the cache adapter bundle on [www.php-cache.com](http://www.php-cache.com).

