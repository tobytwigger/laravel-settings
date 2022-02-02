---
layout: docs 
title: Basic Usage
nav_order: 2
---

# Laravel Settings

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

## Basic Usage

### Getting Values

You can either get a setting value using the facade or the helper function

- `\Settings\Setting::getValue('siteName'); // My App Name`
- `settings('siteName'); // My App Name`

If this is a setting specific to a user, such as light/dark mode selection, you may pass an ID through as the second parameter. If the ID is not given, it will default to `Auth::id()`.

- `settings('darkMode', $user->id); // true or false`

You can also reference every setting directly using the facade without calling `getValue`. 

- `\Settings\Setting::getSiteName()`


### Setting Values

Setting values is just as easy. These can be done through the facade

`\Settings\Setting::setValue('siteName', 'New site name')`

### User/Global Settings

This package supports user and global settings out of the box, along with letting you add your own types. By default, user settings will use the logged in user to retrieve settings for. Pass in an optional ID to `getValue` or `setValue` to override this behavious.

`\Settings\Setting::setValue('darkMode', true, $user->id); // Enable dark mode for a different user`

To set a default setting dynamically, for example if you want to make dark mode enabled by default, you should call `setDefaultValue`. This will set the value for any users who have not overridden it themselves, and will take precedence over the hardcoded default value set when registering the setting.

`\Setting\Setting::setDefaultValue('darkMode', true)`

### Registering settings

All settings must be registered before you can use them. The setting facade gives you a `createUser` and `createGlobal` function to create a user or a global function.

These take two required arguments, which are the key of the setting and the default value of the setting.

`\Setting\Setting::createGlobal(
    key: 'siteName',
    defaultValue: 'My Site Name',
    fieldOptions: null,
    groups: ['branding', 'appearance'],
    rules: ['string', 'max:255']
);`

As above, you may also pass
- An array of groups. These 'categorise' the settings, and are useful when dynamically creating settings pages. We will cover this later.
- Laravel validation rules. Any time you set a setting value, we will validate the value.
