# Upgrade

## Upgrade from 1.0.x to 2.0.0

Your User entity should implement the `Dmishh\SettingsBundle\Entity\SettingsOwnerInterface` interface. In order to maintain
your user setting you need to implement the `getSettingIdentifier` to return the username.

``` php
class MyUser implements SettingsOwnerInterface
{
  // ..

  public function getSettingIdentifier()
  {
    return $this->getUsername();
  }
}
```

You do also need to update your database tables.

* Via [DoctrineMigrationsBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html):**

```bash
php app/console doctrine:migrations:diff
php app/console doctrine:migrations:migrate
```

* Using Doctrine schema update tool

```bash
php app/console doctrine:schema:update --force
```

* The following queries should be executed:

``` sql
DROP INDEX name_user_name_idx ON dmishh_settings;
ALTER TABLE dmishh_settings CHANGE username ownerId VARCHAR(255) DEFAULT NULL;
CREATE INDEX name_owner_id_idx ON dmishh_settings (name, ownerId);
```

### Namespace changed

The "\Bundle" part of the namespace has been removed between `2.0.0-beta1` and `2.0.0-beta2`. The use statements 
and `AppKernel` bundle declaration should be changed:

* Old: `Dmishh\Bundle\SettingsBundle`
* New: `Dmishh\SettingsBundle`
