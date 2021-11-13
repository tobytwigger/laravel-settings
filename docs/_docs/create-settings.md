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


A setting is a class with the following methods,
- The form field for the setting (using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/))
- An array or string of Laravel rules to validate the setting against
- A default value
- A group.

### Example

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
        return \FormSchema\Generator\Field::textInput($this->inputName())->setValue($this->defaultValue());
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
     * @return string
     */
    public static function group(): string
    {
        return 'branding';
    }
}
```

These classes should extend a setting type such as `Settings\Schema\UserSetting` or `Settings\Schema\GlobalSetting`. 

### Form Field

Form fields are defined using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/). You can define any field you need here, including complex fields that return objects.

The input name for the field is defined in `$this->inputName()`, and the default value in `$this->defaultValue()` so to define a simple text field you'd use this plus a label/hint/other fields.

```php
    public function fieldOptions(): \FormSchema\Schema\Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())->setValue($this->defaultValue());
    }

```

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

To define a group, define a `group` function.

```php
    public function group(): string
    {
        return 'branding';
    }
```

See the integrate section for information about how to add metadata to these.

### Tags

Tags can be used to further sort settings. These are simple strings that can later be used to retrieve matching settings.

To define settings, define a `tags` function.

```php
    public function tags(): array
    {
        return ['appearance', 'text', 'language'];
    }
```

### Encryption

If the data in the setting is sensitive, it can be encrypted automatically using the Laravel encryption tools. To turn on encryption for a setting, make sure to implement the `\Settings\Contracts\ShouldEncrypt` interface on the setting.

### Complex data types

All values in the database are automatically serialised to preserve type. This means that arrays and objects will all be saved and retrieved in the correct format, so you don't have to worry about how your setting is saved.

If you want to control how the setting is saved in the database, define `castToString` and `castToValue` functions on the setting which will convert your validated setting value to a database-friendly string and back.

This example would handle an array (though would be pointless since arrays are already casted automatically).

```php
    public function castToString(array $value): string
    {
        return json_encode($value);
    }

    public function castToValue(string $value): array
    {
        return json_decode($value, true);
    }
```

## Registering 

You can then register settings in the `boot` function of a service provider using the facade, or replace `\Settings\Setting::` with `settings()->` to use the helper function.

You can also register information about groups, which will be automatically pulled into any form schemas you extract from settings.

```php
    public function boot()
    {
        \Settings\Setting::register(\Acme\Setting\SiteName::class);
        \Settings\Setting::register([
            \Acme\Setting\SiteName::class,
            \Acme\Setting\SiteTheme::class
        ]);
        
        \Settings\Setting::register(\Acme\Setting\SiteName::class, ['extra', 'tags', 'for', 'the', 'setting']);
        
        \Settings\Setting::registerGroup(
            'branding', // Group Key
            'Branding', // Title for the group
            'Settings related to the site brand' // Description for the group
        );
    }
```

You can also register settings and groups in the config

```php
<?php

// config/settings.php

return [

    'settings' => [
        \Acme\Setting\SiteName::class,
        \Acme\Setting\SiteTheme::class,
        ...
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

To create a new type, create an abstract class that implements `Settings\Contracts\SettingType`. 

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
