---
layout: docs
title: JavaScript
nav_order: 4
---

# JavaScript
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

Settings can easily be retrieved in blade templates as normal. If you're using a frontend framework like Vue, you can pass through settings to your root Vue component from your blade template.

If you use Vue, we've put together a package to make this easier. This allows you to get and set any setting from any Vue component without having to pass it through from parent components.

We currently only support Vue, but let us know if you're interested in using this package with a different framework.

## Vue

### Setup

First you'll need to install the package using npm or yarn.

```shell
npm install --save @elbowspaceuk/laravel-settings-vue
```

In your `app.js` file, where you create your Vue instance, add the following to initialise the plugin.

```js
// app.js
import Settings from '@elbowspaceuk/laravel-settings-vue';

Vue.use(Settings, {
    axios: axios
});
```
Note you must pass an axios instance to the Settings plugin. This must be ready to make api calls - if you are using the standard Laravel template this is set up for you and bound to `window.axios`, so the above snippet will work.

You should also add the `\Settings\Http\Middleware\ShareSettingsWithJs` middleware to your `web` group in `app/Http/Kernel.php`, and add `@settings` to the head of your base blade template.

```blade
// layout.blade.php
<head>
    <title>...</title>
    @settings
</head>
```

### Getting

From any Vue component, in the template or a method/computed property/watcher, you have access to a `$setting` property and a `$settings` property.

The `$setting` property contains a key-value pair object with all the available settings and their values. This is reactive, so you can use it anywhere in your component.

The `$settings` property contains a set of functions to help you work with settings in js.

```vue
<template>
    <div>Your theme is { { $setting.theme } }</div>
    <div>THrough a computed property it's the same: {{currentTheme}}</div>
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

### Setting

To set a setting value you should call `this.$settings.setValue('site_name', 'My new site name')` in your Vue component. You can set multiple at a time by passing through an object of key-value pairs of settings to `this.$settings.setValues()`.

### Loading

In order to make your settings available to the frontend, you need to tell laravel settings you want to make use of the setting. This prevents us loading every setting every time which could be slow. There are two ways to share your settings with your Vue component. You can either do it from your component directly, or eager load them by specifying which settings to load in your Laravel app.

#### Loading through JS

Before you can make use of a setting, call `this.$settings.loadSetting('theme')` and pass it the setting key or alias to load. This will be loaded in the background. During loading `this.$setting.theme` will be undefined, but once the setting is ready it will reactively update to the value.

You can load many settings at the same time with `this.$settings.loadSettings(['theme', 'site_name'])`.

#### Eager Loading

To avoid the overhead of loading settings from your javascript, you should try and mark settings to share in your laravel app. Any settings loaded this way will be instantly available without having to load them with `loadSettings()`

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
\Settings\Loading\LoadedSettings::eagerLoad('site_name');
```
