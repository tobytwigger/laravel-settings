---
layout: docs
title: Settings UI
nav_order: 8
---

# Settings UI

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

## Query settings

To display the settings to users, you need to get information about the registered settings. These all use the setting service class, which you can access through the facade or the helper (passing it no parameters).

**Groups**
- Get all settings with a given group: `\Settings\Setting::withGroup('group-name')->get()`
- Get all settings with at least one of the given groups: `settings()->withAnyGroup(['group-name', 'group-name-2'])->get()`
- Get all settings that have all the given groups: `settings()->withAllGroups(['group-name', 'group-name-2'])->get()`

**Types**
- Get all settings of a certain type: `settings()->withType(\Acme\Setting\TeamSettingType::class)->get()` or `settings()->withType('team')->get()`.
- Get all global settings: `settings()->withGlobal()->get()`. This is the same as calling `settings()->withType(\Settings\Schema\GlobalSetting::class)`.
- Get all user settings: `settings()->withUser()->get()`.


The functions can be chained, so to get all global settings that belong to a group called 'Blog Module', you'd use `\Settings\Setting::withGlobal()->withGroup('blog-module')->get()`.

## Create a form instance

`get()` will always return a `Settings\Support\SettingCollection` instance. You can use this like a normal Laravel collection, but you will also have access to the following functions.

- `toForm()` - turn the settings into a `\FormSchema\Schema\Form`.
- `toKeyValuePair()` - get all settings and their values as key value pairs.

### Custom form creator

When using `toForm`, you can change how a collection casts settings to a form. Define a callback in the `register` function of your service provider, which accepts a collection of settings and returns a Form Schema instance.

```php
public function register()
{
    \Settings\Collection\SettingCollection::$convertToFormUsing = function(\Settings\Collection\SettingCollection $settings) {
        return \FormSchema\Generator\Form::make()->withGroup(<...>)->form();
    }
}
```

## Using the Form

{: .label .label-yellow }
This section is incomplete

Pass this schema to the frontend and render it using a dynamic form generator. Each setting group will appear in a different form group.
