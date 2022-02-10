---
layout: docs
title: Setting Types
nav_order: 6
parent: Advanced
---


# Setting Types
{: .no_toc }

<details open markdown="block">
  <summary>
    Contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---


## What are setting types

By default, we provide a user setting type and a global setting type. The global setting type is set once within your application, and will be the same for all authenticated users. But a user setting will return a different value depending on which user is logged in, meaning a user can control the setting themselves.

For some sites, the settings will depend on the team a user is in, the module you're operating in, or the country you're in. When creating a setting, you can assign it to be of one type. Whenever you then get the value of that setting, it will depend on the model logged in.

## Configure user settings

By default, the user settings uses the Laravel `Auth` facade to resolve the user ID. If your app gets users a different way, you can override this functionality with a callback in the register function of your service provider.

```php
    `\Settings\Types\UserSetting::$resolveUserUsing = fn() => \Auth::driver('api')->id();`
```

## Creating a custom type

To create a new type, create an abstract class that implements `Settings\Contracts\SettingType`. This should implement 
at least the `resolveId` function. This function takes no arguments, and should return the ID of the currently logged in user/team etc, or null if not logged in.

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

To use your custom type, simply create a new setting class that extends your type. This replaces the user or global setting class you usually extend.

If you use anonymous settings you can't extend this class. Instead, you should create the type in your service provider as follows.

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

This can then be used when registering the anonymous setting. Instead of `\Setting\Setting::createGlobal` or `createUser`, simply call `\Setting\Setting::create(team: 'team', ...)`; and pass through the type key as the first argument.

If the setting type is only going to be used in one setting and you don't want to create a type, you can just implement the `resolveId` function directly in your setting, or pass it into the `create` function as the `resolveIdUsing` function. The `type` can be anything you want and won't be used.

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

