---
layout: docs 
title: Class-based Settings
nav_order: 3
---

# Class-based Settings

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

## Introduction

Although the settings we've covered work well for most sites, sometimes you want to make each of your settings a little more explicit to help you keep track of them.

This is where class-based settings come in. Rather than a setting being represented with a key, your setting is instead a class. This not only allows you to benefit from typehinting when getting and setting values, but also gives a few useful features you can make use of.

## Defining a class setting

We recommend keeping all your settings in an `app/Settings` directory. Using classes, each setting is just a class that contains all the information we need.

```php
<?php

use Settings\Schema\UserSetting;
use Settings\Schema\GlobalSetting;

class SiteName extends GlobalSetting
{

    /**
     * The default value of the setting.
     *
     * @return mixed
     */
    public function defaultValue()
    {
        return 'My Site Name';
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

## Getting/setting values

You can use class-based settings exactly the same as anonymous settings. Instead of a key such as `siteName`, you can use the class name instead.

`\Settings\Setting::getValue(\App\Settings\SiteName::class)`

You can also use the class directly to avoid needing the key

`\App\Settings\SiteName::getValue()`.

## Registering settings

You can then register settings in the `boot` function of a service provider using the facade or helper function.

You can also register information about groups, which will be automatically pulled into any form schemas you extract from settings.

```php
    public function boot()
    {
        \Settings\Setting::register(new \App\Setting\SiteName());
        \Settings\Setting::register([
            // Create a new class instance manually
            new \App\Setting\SiteName(),
            
             // Letting the service container build the setting means you can inject dependencies into the setting construct.
            $this->app->make(\App\Setting\SiteTheme::class)
        ]);
        
        \Settings\Setting::register(new \App\Setting\SiteName(), ['extra', 'groups', 'for', 'the', 'setting']);
        
        \Settings\Setting::registerGroup(
            'branding', // Group Key
            'Branding', // Title for the group
            'Settings related to the site brand' // Description for the group
        );
    }
```

You can also register settings and groups in the config. You need to make sure these settings can be resolved from the service container - if your setting doesn't rely on any dependencies being passed in then you won't need to worry about this.

```php
<?php

// config/settings.php

return [

    'settings' => [
        \App\Setting\SiteName::class,
        \App\Setting\SiteTheme::class,
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
        'branding' => [
            'title' => 'Branding',
            'subtitle' => 'Settings related to the site brand'
        ],
    ]
];
```


## Settings in depth

### Permissions

You can control who can update and read certain settings. By default anyone can update or read any settings (depending on how you let people use the settings). If you want to limit this for added protection, you can add a `canWrite` and `canRead` function to class-based settings.

These take no arguments and should return a boolean.

```php
public function canRead()
{
    return Auth::check() && Auth::user()->can('update-this-setting');
}
```

### Form Field

Form fields are defined using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/). You can define any field you need here, including complex fields that return objects.

The input name for the field is defined in `$this->key()`, and the default value in `$this->defaultValue()` so to define a simple text field you'd use this plus a label/hint/other fields.

When using anonymous settings, hardcode the key and value and just pass the result of the field generator directly to `::create`.

```php
    public function fieldOptions(): \FormSchema\Schema\Field
    {
        return \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue());
    }

```

Fields are currently a required property of any setting, to allow you to dynamically create setting pages.

### Validation

To ensure the settings entered into the database are valid, you can define rules in the `rules` array. This can be an array or string of rules, that will validate a valid value. There's no need to put `required`/`optional` rules in, but do include `nullable` if the option can be null.

```php
    public function rules(): array|string
    {
        return 'string|min:2|max:20';
    }
```

### Groups

Groups are a way to order settings to the user. By grouping together similar settings (such as those related to the site theme, authentication, emails etc), it helps users quickly find what they're looking for.

To define a group, define a `group` function. This should return an array of groups the setting is in. When retrieving a form schema to represent settings, the first group will be taken as the group, and therefore the first group should be the 'main' group.

```php
    public function group(): array
    {
        return ['branding', 'appearance'];
    }
```

See the integrate section for information about how to add metadata to these.

### Encryption

The value of all settings are encrypted automatically, since it adds very little overhead. If the data in the setting is not sensitive and you'd rather not encrypt it, set a public `$shouldEncrypt` property to false in your setting.

```php
    protected boolean $shouldEncrypt = false;
```

You can also make the default behaviour be that encryption is not automatic, but can be turned on with `$shouldEncrypt = true`. To do this, set `encryption` to `false` in the config file. Anonymous settings use this default behaviour to determine if settings should be encrypted.

### Complex data types

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


## Migrating to class-based from anonymous

Often you will start using anonymous settings and move to class-based settings as your application grows. To make this as simple as possible, you can alias a class-based setting and use it as though it was an anonymous setting.

You can alias a setting through config or the service provider

```php
<?php

// config/settings.php
return [
    'aliases' => [
        'siteName' => \App\Setting\SiteName::class,
        ...
    ]
];

// app/Providers/AppServiceProvider.php
public function boot()
{
    settings()->alias('siteName', \App\Setting\SiteName::class);
    \Settings\Setting::alias('siteName', \App\Setting\SiteName::class);
}
```

You can now use the site name setting as though it had a key `siteName`. You can now access it in the following ways
- `settings('siteName')`
- `\Settings\Setting::getValue('siteName')`
- `\Settings\Setting::getSiteName()`
- `settings()->getValue('siteName')`
- `settings()->getSiteName()`
- `\App\Settings\SiteName::getValue()`
- `\Settings\Setting::getValue(\App\Settings\SiteName::class)`
