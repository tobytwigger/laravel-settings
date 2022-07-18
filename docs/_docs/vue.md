---
layout: docs
title: Vue
nav_order: 4
---

# Vue
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

If you're using Vue, we've put together a package to let you access and change settings directly from your components. 

## Setup

**Install**
First you'll need to install the package using npm or yarn.

```shell
npm install --save @tobytwigger/laravel-settings-vue
yarn add @tobytwigger/laravel-settings-vue
```

**Initialise**
In your `app.js` file, where you create your Vue instance, add the following to initialise the plugin.

```js
// app.js
import Settings from '@tobytwigger/laravel-settings-vue';

Vue.use(Settings, {
    axios: axios
});
```
Note you must pass an axios instance to the Settings plugin. This must be ready to make api calls - if you are using the standard Laravel template this is set up for you and bound to `window.axios`, so the above snippet will work.

**Eager load settings**
You should also add the `\Settings\Http\Middleware\ShareSettingsWithJs` middleware to your `web` group in `app/Http/Kernel.php`.

```php
protected $middlewareGroups = [
    'web' => [
        ...
        \Settings\Http\Middleware\ShareSettingsWithJs::class,
    ],
];
```

**Share settings**
Finally, add `@settings` to the head of your base blade template.

```blade
// layout.blade.php
<head>
    <title>...</title>
    @settings
</head>
```

## Getting values

From any Vue component, in the template or a method/computed property/watcher, you have access to a `$setting` property and a `$settings` property.

The `$setting` property contains a key-value pair object with all the available settings and their values. This is reactive, so you can use it anywhere in your component.

The `$settings` property contains a set of functions to help you work with settings in js.

```vue
<template>
    <div>Your theme is { { $setting.theme } }</div>
    <div>Through a computed property it's the same: {{currentTheme}}</div>
</template>
<script>
    export default {
        computed: {
            currentTheme() {
                return this.$setting.theme;
            }
        }
    }
</script>
```

## Setting values

When you set a setting, we make an API call in the background to update the setting on your server. For this to work, you must not have disabled the API in the configuration, and you should ensure API calls can be made using `axios`.

To set a setting value you should call `this.$settings.setValue('site_name', 'My new site name')` in your Vue component. You can set multiple at a time by passing through an object of key-value pairs of settings to `this.$settings.setValues()`.

You can also just set `$setting` directly with `this.$setting.site_name = 'My New Site Name'`.

If you're working with an input that uses v-model, you can use the setting directly. This will automatically update the value in your database when v-model is triggered.

```vue
<template>
    <input type="checkbox" v-model="$setting.dark_mode" />
</template>
```

## Loading

To avoid the overhead of loading settings from your javascript you can eager load settings you know you'll be using. Any settings loaded this way will be instantly available without additional API calls.

For settings that should be loaded on every request, such as a site name, you can put them into the config

```php
[
    ...,
    'js' => [
        'autoload' => [
            'site_name'
        ]
    ]
]
```

For settings that should only be loaded on some requests, add this to your controller or middleware.

```php
\Settings\Share\LoadedSettings::eagerLoad('site_name');
```
