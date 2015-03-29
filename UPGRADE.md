# Upgrade

## Upgrade from 1.0.x to 2.0.0

Your User entity should implement the `Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface` interface. In order to maintain
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

**1) Via [DoctrineMigrationsBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html):**

```bash
php app/console doctrine:migrations:diff
php app/console doctrine:migrations:migrate
```

**2) Manually:**

```bash
php app/console doctrine:schema:update --force
```

**3) Queries:**

The following queries should be executed:
``` sql
DROP INDEX name_user_name_idx ON dmishh_settings;
ALTER TABLE dmishh_settings CHANGE username ownerId VARCHAR(255) DEFAULT NULL;
CREATE INDEX name_owner_id_idx ON dmishh_settings (name, ownerId);
```