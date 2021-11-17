---
layout: docs 
title: Creating Settings
nav_order: 3
---

# Creating a Setting

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

## Create a new setting

Any setting must have at least the following information:

- A type (user, global or another custom type)
- A key
- A default value
- The form field for the setting (using
  the [form schema generator](https://tobytwigger.github.io/form-schema-generator/))

For smaller apps, you can use simple anonymous setting keys to get going quickly. These require less boilerplate code
to get running, but it's up to you to manage the keys and make sure you don't re-use any.

For larger apps, or as your app grows, you can migrate over to using class-based settings. These give you more control
over your settings and their appearance and use, whilst also leveraging your IDE to give you typehinting for available
settings and make setting name clashes impossible.

### Anonymous settings

To create a new anonymous setting, you can define it in the `boot` method of your service provider.

```php
    public function boot()
    {
        \Settings\Setting::createGlobal(
            'siteName', // The key
            'My App', // The default value
            \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue()), // The form field
            ['branding', 'appearance'], // The groups the setting is in
            ['string'], // The laravel validation rules
       )
    }
```

You can use `createGlobal` or `createUser` by default, and if you add a custom type reference it with `\Settings\Setting::create('custom-type', 'siteName', ...)`.

### Class-based settings

A class-based setting has generally the same information, but defines functions to return them rather than setting them in arguments.

```php
<?php

use Settings\Schema\Setting;

class SiteName extends Setting
{

    /**
     * The default value of the setting.
     *
     * @return mixed
     */
    public function defaultValue()
    {
        return 'My Site';
    }

    /**
     * The field schema to show the user when editing the value.
     *
     * @throws \Exception
     * @return Field
     */
    public function fieldOptions(): \FormSchema\Schema\Field
    {
        return \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue());
    }

    /**
     * Return the validation rules for the setting.
     *
     * The key to use for the rules is data. You may also override the validator method to customise the validator further
     *
     * @return array
     */
    public function rules(): array|string
    {
        return 'string|min:2|max:20';
    }
    
    /**
     * @return array
     */
    public static function group(): array
    {
        return ['branding', 'appearance'];
    }
}
```

These classes should extend a setting type such as `Settings\Schema\UserSetting` or `Settings\Schema\GlobalSetting`. See more about creating a custom type at the end of this page.

### Customising your setting

#### Form Field

Form fields are defined using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/). You can define any field you need here, including complex fields that return objects.

The input name for the field is defined in `$this->key()`, and the default value in `$this->defaultValue()` so to define a simple text field you'd use this plus a label/hint/other fields.

When using anonymous settings, hardcode the key and value and just pass the result of the field generator directly to `::create`.

```php
    public function fieldOptions(): \FormSchema\Schema\Field
    {
        return \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue());
    }

```

Fields are currently a required property of any setting, to allow you to dynamically create setting pages. You can learn more about how to integrate your frontend with the form schema in the [integrate documentation](integrating.md).

#### Validation

To ensure the settings entered into the database are valid, you can define rules in the `rules` array. This can be an array or string of rules, that will validate a valid value. There's no need to put `required`/`optional` rules in, but do include `nullable` if the option can be null.

```php
    public function rules(): array|string
    {
        return 'string|min:2|max:20';
    }
```

#### Groups

Groups are a way to order settings to the user. By grouping together similar settings (such as those related to the site theme, authentication, emails etc), it helps users quickly find what they're looking for.

To define a group, define a `group` function. This should return an array of groups the setting is in. When retrieving aform schema to represent settings, the first group will be taken as the group, and therefore the first group should be the 'main' group.

```php
    public function group(): array
    {
        return ['branding', 'appearance'];
    }
```

See the integrate section for information about how to add metadata to these.

#### Encryption

The value of all settings are encrypted automatically, since it adds very little overhead. If the data in the setting is not sensitive and you'd rather not encrypt it, set a public `$shouldEncrypt` property to false in your setting.

```php
    protected boolean $shouldEncrypt = false;
```

You can also make the default behaviour be that encryption is not automatic, but can be turned on with `$shouldEncrypt = true`. To do this, set `encryption` to `false` in the config file. Anonymous settings use this default behaviour to encrypt or not encrypt settings.

#### Complex data types

All values in the database are automatically serialised to preserve type. This means that arrays and objects will all be saved and retrieved in the correct format, so you don't have to worry about how your setting is saved.

If you want to control how the setting is saved in the database, implement the `\Settings\Contract\CastsSettingValue` interface on your setting. You will need to define a `castToString` and `castToValue` functions on the setting which will convert your validated setting value to a database-friendly string and back.

This example would handle a complex data object, such as something returned from an API client.

```php
    public function castToString(\My\Api\Result $value): string
    {
        return json_encode([
            'id' => $value->getId(),
            'result' => $value->getResult()
        ]);
    }

    public function castToValue(string $value): \My\Api\Result
    {
        $value = json_decode($value, true);
        
        return new \My\Api\Result($value['id'])
            ->getResult($value['result']);
    }
```

If the setting value cannot be serialized, it cannot be used with an anonymous setting, so you'll have to implement it as a class using casting.

## Registering

You can then register settings in the `boot` function of a service provider using the facade, or replace `\Settings\Setting::` with `settings()->` to use the helper function.

You can also register information about groups, which will be automatically pulled into any form schemas you extract from settings.

```php
    public function boot()
    {
        \Settings\Setting::register(new \Acme\Setting\SiteName());
        \Settings\Setting::register([
            // Create a new class instance manually
            new \Acme\Setting\SiteName(),
            
             // Letting the service container build the setting means you can inject dependencies into the setting construct.
            $this->app->make(\Acme\Setting\SiteTheme::class)
        ]);
        
        \Settings\Setting::register(new \Acme\Setting\SiteName(), ['extra', 'groups', 'for', 'the', 'setting']);
        
        \Settings\Setting::registerGroup(
            'branding', // Group Key
            'Branding', // Title for the group
            'Settings related to the site brand' // Description for the group
        );
    }
```

Anonymous settings are automatically registered for you when using `create::`, `createUser::` or `createGlobal::`. If you want to just create a setting rather than register one, you can use the factory directly.

```php
$setting = \Settings\Anonymous\AnonymousSettingFactory::make(string $type, string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null);
\Setting\Setting::register($setting);
```

You can also register settings and groups in the config. You need to make sure these settings can be resolved from the service container - if your setting doesn't rely on any dependencies being passed in then you won't need to worry about this.

```php
<?php

// config/settings.php

return [

    'settings' => [
        \Acme\Setting\SiteName::class,
        \Acme\Setting\SiteTheme::class,
        [ // An anonymous setting
            'type' => 'user', // 'user', 'global', or a custom type
            'key' => 'timezone', // The setting key
            'defaultValue' => 'Europe/London', // The default value
            // The field. You must serialize this so your config can still be cached.
            'fieldOptions' => serialize(\FormSchema\Generator\Field::textInput('timezone')->setValue('Europe/London')),
            'groups' => ['language', 'content'], // Groups to put the setting in
            'rules' => ['string|timezone'] // Laravel validation rules to check the setting value       
        ]
    ],
    'groups' => [
        'branding' [
            'title' => 'Branding',
            'subtitle' => 'Settings related to the site brand'
        ],
    ]
];
```

## Advanced

### Setting Types

By default, you have the setting types global and user. The global setting type is set once, and will be the same for everyone. But a user setting will return a different value depending on which user is logged in.

For some sites, the settings will depend on the team a user is in, the module you're operating in, or the country you're in. When creating a setting, you can assign it to be of one type. Whenever you then get the value of that setting, it will depend on the model logged in.

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
