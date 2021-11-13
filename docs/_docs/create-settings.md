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


A setting is a class that extends `Settings\Schema\UserSetting` or `Settings\Schema\GlobalSetting`. You will have to define the form field for the setting (using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/)), and an array or string of Laravel rules to validate the setting against, and a default value and a group.

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
    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName());
    }

    /**
     * Return the validation rules for the setting.
     *
     * The key to use for the rules is data. You may also override the validator method to customise the validator further
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            $this->inputName() => 'string|min:2|max:20'
        ];
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

A setting is a class that extends a setting type such as `Settings\Schema\UserSetting` or `Settings\Schema\GlobalSetting`. 

### Form Field

Using the [form schema generator](https://tobytwigger.github.io/form-schema-generator/)

Mention default variable use

### Validation

### Groups

Groups are a way to order settings to the user. By grouping together similar settings (such as those related to the site theme, authentication, emails etc), it helps users quickly find what they're looking for.

### Tags

What are they, adding to the setting with `tags()`

### Encryption

### Complex data types

Saving arrays and objects, like normal.

Can override the serialisation method with `toString` and `toValue` set on the setting?

## Registering 

- How to register a setting
- How to add extra tags

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
