---
layout: docs
title: Setting Types
nav_order: 3
---

# Setting Types

{: .no_toc }

<details open markdown="block">
  <summary>
    Table of contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---

## What are setting types

By default, you have the setting types global and user. The global setting type is set once, and will be the same for everyone. But a user setting will return a different value depending on which user is logged in.

For some sites, the settings will depend on the team a user is in, the module you're operating in, or the country you're in. When creating a setting, you can assign it to be of one type. Whenever you then get the value of that setting, it will depend on the model logged in.

## Creating a Type

### Customising auth

By default, the user settings uses the Laravel `Auth` facade to resolve the user ID. If your app gets users a different way, you can override this functionality with a callback in the register function of your service provider.

```php
    `\Settings\Types\UserSetting::$resolveUserUsing = fn() => \Auth::driver('api')->id();`
```

### Class-based settings

To create a new type, create an abstract class that implements `Settings\Contracts\SettingType`. You can then use this setting type by extending the new class in your setting.

```php
abstract class TeamSettingType implements \Settings\Contracts\SettingType
{

    /**
    * Get the ID of the currently logged in model (in this case, the team id)
    * 
    * Returning null will just return the setting value as the default value.
    * 
    * @return int|null
     */
    public function resolveId(): ?int
    {
        if(\App\Team\Resolver::hasCurrentTeam()) {
            return \App\Team\Resolver::currentTeam()->id();        
        }
        return null;
    }

}
```

### Anonymous settings

For anonymous classes which don't extend a type, you can define an alias instead. To define a team type to use in your anonymous settings, that can be used in place of 'global' and 'user', call this in your `boot()` method in the service provider.

```php
public function boot()
{
    \Settings\Anonymous\AnonymousSettingFactory::mapType(
        // The key to refer to the type as
        'team',
        // Return the current team ID, or null if there is no team. This will be used to filter the settings.
        fn() => \App\Team\Resolver::hasCurrentTeam() ? \App\Team\Resolver::currentTeam()->id() : null
    );
}
```

If the setting is a one-off and you don't want to create a type, you can override the function used to resolve the ID by passing in a final parameter when creating the anonymous setting.

```php
    public function boot()
    {
        \Settings\Setting::create(
            'team', // Although you still have to define a type, it doesn't mean any thing and doesn't have to exist. This can be useful for retrieving settings though.
            'siteName', // The key
            'My App', // The default value
            \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue()), // The form field
            ['branding', 'appearance'], // The groups the setting is in
            ['string'], // The laravel validation rules,
            fn() => \App\Team\Resolver::hasCurrentTeam() ? \App\Team\Resolver::currentTeam()->id() : null
       )
    }
```
