---
layout: docs 
title: Configuration
nav_order: 6
---

# Configuration

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

All configuration is controlled from the `config/laravel-settings.php` configuration file. If this does not exist in your app, run `php artisan vendor:publish --provider="Settings\SettingsServiceProvider"` to publish the file.

## Table name

```php
    [
        'table' => 'settings',
    ]
```

This controls the name of the table. Make sure you don't change it once you've migrated the tables, as the change won't be picked up again.

## Cache

```php
[
    'cache' => [
        'ttl' => null
    ],
]
```

You can set how long a setting value should be cached for, in seconds. We automatically refresh the cache when a setting is set, so this can be as high as you'd like. If `ttl` is null, the cache will never expire.

## Encryption

```php
[
    'encryption' => [
        // Should all settings be encrypted by default? This can be overridden on each individual setting.
        'default' => false
    ],
]
```

Set if all settings should be encrypted by default. The overhead to encrypting a setting is relatively small.

## Registering Settings

```php
[
   'settings' => [
        \App\Settings\DarkMode::class,
        [
            'type' => 'user',
            'key' => 'timezone',
            'defaultValue' => 'Europe/London',
            'fieldOptions' => serialize(\FormSchema\Generator\Field::textInput('timezone')->setValue('Europe/London')),
            'groups' => ['language', 'content'],
            'rules' => ['string', 'timezone']
        ]
    ],
]
```

Especially if your app is small, you may decide to register all your settings in the config.

If you have class-based settings, simply put the full class name into the `settings` array.

For an anonymous setting, the following values are accepted

- `type`: One of `user` or `global`, or a custom type.
- `key`: The key for the setting.
- `defaultValue`: The default value of the setting.
- `fieldOptions` (optional): Either the field for the setting, wrapped in the php `serialize` function, or null.
- `groups` (optional): An array of groups the setting belongs in
- `rules` (optional): The validation rules for the setting

## Groups

```php
[
    'groups' => [
        'branding' => [
            'title' => 'Branding',
            'subtitle' => 'Settings related to the site brand'
        ],
    ],
]
```

When you turn settings into a form schema to show to a user, we group settings together by their main group. By registering information about the group in config, we will pass this along with the settings.

## Aliases

```php
[
    'aliases' => [
         'site_name' => \My\Settings\SiteName::class
    ],
]
```

Aliases let you refer to a class-based setting with a key. Although you'd usually set the alias on the setting itself, you can set the alias in the config instead. This also lets you register multiple aliases for a setting.

## API

```php
[
    'routes' => [
        'api' => [
            'enabled' => true,
            'prefix' => 'api/settings',
            'middleware' => []
        ]
    ],
]
```

These configuration options all apply to the API. The API is used to load and update settings from the frontend. Without it, you can still eager-load settings but you won't be able to update or dynamically load them from Vue.

- `enabled` - Should the API be enabled at all?
- `prefix` - The URL of the API will start with this
- `middleware` - An array of any middleware to run the settings API through. If you add `auth` here, keep in mind non logged in users won't be able to load or update settings from Vue, which may affect parts of your app.

## JS

```php
[
    'js' => [
        'autoload' => [
            'dark_mode'
        ]
    ]
]
```

Any setting keys in `autoload` will be loaded and passed to your Vue instance on every page load. Any settings you use regularly should be in here to avoid excess API calls.
