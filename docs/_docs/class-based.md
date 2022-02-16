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

The basic settings covered so far are called anonymous settings, and they are a very flexible way of managing settings. As your app starts to grow, you may find you start forgetting which setings you have created.

Class-based settings are just a simple class containing information about the setting. This not only lets you benefit from typehinting during development and limits key clashes, but it also gives you a few extra customisations for your settings.

Both methods of creating settings are valid, and often you'll find yourself using a mixture of the two. They are both fundamentally the same, so there are no large feature differences between them.

## Defining a class setting

We recommend keeping all your settings in an `app/Settings` directory. Each setting should be its own class which extends either `Settings\Schema\UserSetting` or `Settings\Schema\GlobalSetting`. Your IDE should tell you which methods you need to implement.

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

You can use class-based settings exactly the same as anonymous settings. Instead of a key such as `site_name`, you can use the class name instead.

`\Settings\Setting::getValue(\App\Settings\SiteName::class)`

You can also use the class directly

`\App\Settings\SiteName::getValue()`.

## Registering settings

Once you've created a setting you must register it, so your app knows what settings are available. This is usually done in the `boot` function of a service provider using the facade or helper function.

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
    }
```

## Settings in depth

### Permissions

You can control who can update and read specific settings. By default anyone can update or read any settings. If you want to limit this for added protection, you can add a `canWrite` and `canRead` function to class-based settings.

These take no arguments and should return a boolean.

```php
public function canRead(): bool
{
    return Auth::check() && Auth::user()->can('read-this-setting');
}

public function canWrite(): bool
{
    return Auth::check() && Auth::user()->can('write-this-setting');
}
```

We will take care of ensuring a user has permission before they update a setting.

### Form Field

Form fields are defined using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/). This allows you to define the form field to update the setting alongside the rest of the setting definition, then automatically create settings pages for your users.

You can define any field you need here, including complex fields. The input name for the field is defined in `$this->key()`, and the default value in `$this->defaultValue()`.

When using anonymous settings, hardcode the key and value and just pass the result of the field generator directly to `::create`.

```php
    // app/Settings/Class.php
    public function fieldOptions(): \FormSchema\Schema\Field
    {
        return \FormSchema\Generator\Field::textInput($this->key())->setValue($this->defaultValue());
    }
    
    // ServiceProvider.php
    \Settings\Setting::createGlobal(..., fieldOptions: \FormSchema\Generator\Field::textInput('mykey')->setValue('myvalue'))

```

Fields are currently a required property of any setting, to allow you to dynamically create setting pages.

### Validation

To ensure the settings entered into the database are valid, you can define an array or string of Laravel rules in the `rules` method. If a setting is updated that does not match validation, a validation exception will be thrown and the setting not saved.

There's no need to put `required`/`optional` rules in, but do include `nullable` if the option can be null.

```php
    public function rules(): array|string
    {
        return 'string|min:2|max:20';
    }
```

### Groups

Groups are a way to order settings to the user. By grouping together similar settings (such as those related to the site theme, authentication, emails), it helps users quickly find what they're looking for.

The `group` function should return an array of groups the setting is in. When retrieving a form schema to represent settings, the first group will be taken as the 'main' group.

```php
    public function group(): array
    {
        return ['branding', 'appearance'];
    }
```

To further pad out your settings page, you can add a name and description to any group in your service provider. 

```php
    \Settings\Setting::registerGroup(
        'branding', // Group Key
        'Branding', // Title for the group
        'Settings related to the site brand' // Description for the group
    );
```

### Encryption

To protect sensitive settings in the database, values can automatically be encrypted and decrypted for you. You can mark a setting as sensitive by adding a `$shouldEncrypt` property to the setting class.

```php
    protected boolean $shouldEncrypt = true;
```

Encrypting and decrypting add very little overhead, so you may automatically encrypt all settings unless otherwise specified. To do this, set `encryption` to `false` in the config file. Anonymous settings use this behaviour to determine whether they should be encrypted.

```php
// config/laravel-settings.php
return [
    ...,
    'encryption' => [
        'default' => false
    ]
]
```

### Complex data types

All values in the database are automatically serialised to preserve type. This means that arrays and objects will all be saved and retrieved in the correct format, so you can always retrieve the value of a setting exactly the same as it was when you set it.

All primitive types can be serialized already so you don't need to worry about this. If you want to save a complex object and control how it is saved in the daabase, you can implement the `\Settings\Contract\CastsSettingValue` interface on your setting.

You will need to define a `castToString` and `castToValue` functions on the setting which will convert your validated setting value to a database-friendly string and back. This example would handle a complex data object, such as something returned from an API client.

```php
    /**
    * Turn the API result into a string that can be saved in the database
    */
    public function castToString(\My\Api\Result $value): string
    {
        return json_encode([
            'id' => $value->getId(),
            'result' => $value->getResult()
        ]);
    }

    /**
    * Turn the string back into an instance of the API result class
    */
    public function castToValue(string $value): \My\Api\Result
    {
        $value = json_decode($value, true);
        
        return new \My\Api\Result($value['id'])
            ->setResult($value['result']);
    }
```

## Migrating to class-based from anonymous

Often you will start using anonymous settings and move to class-based settings as your application grows. To make this as simple as possible, you can alias a class-based setting and use it as though it was an anonymous setting.

To migrate over to class-based settings, you should create each setting as a class-based setting and add an alias, with a key matching the anonymous setting. Then, delete the anonymous setting creation function call in your service provider.

You can alias a setting through config or the service provider, or define it directly on your setting class. You only need to use one of the following methods.

```php
// config/settings.php
return [
    'aliases' => [
        'site_name' => \App\Setting\SiteName::class,
        ...
    ]
];
```

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    settings()->alias('site_name', \App\Setting\SiteName::class);
    \Settings\Setting::alias('site_name', \App\Setting\SiteName::class);
}
```

```php
// app/Settings/SiteName.php

public function alias(): ?string
{
    return 'site_name';
}

```

You can now use the site name setting as though it had a key `siteName`, and so access the setting in the following ways.

**Same as anonymous**
- `settings('site_name')`
- `\Settings\Setting::getValue('site_name')`
- `\Settings\Setting::getSiteName()`
- `settings()->getValue('site_name')`
- `settings()->getSiteName()`

**Only for class-based**
- `\App\Settings\SiteName::getValue()`
- `\Settings\Setting::getValue(\App\Settings\SiteName::class)`
