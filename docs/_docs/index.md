---
layout: docs
title: Introduction
nav_order: 1
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

## Introduction

Laravel Settings provides simple but flexible settings to any Laravel app.

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```console
composer require twigger/laravel-settings
```

You can publish the configuration file by running 
```console
php artisan vendor:publish --provider="Settings\SettingsServiceProvider"
```

## Basic Usage

### Get a setting

```php
    echo \Settings\Setting::getValue('siteName') // My App
```

### Create a setting

You can create settings in the service provider, in your `boot` method

```php
    public function boot()
    {
        \Settings\Setting::createGlobal('siteName', 'My App', Field::text('siteName')->setValue('My App')->setLabel('The name of the site'));
        \Settings\Setting::createUser('theme', 'default', Field::select('theme')->setValue('default')->setLabel('The theme to use')->withOption('default', 'Default'));
    }
```

#### Class-based settings

Class based settings let you do everything you can with normal settings, as well as letting you
- Use complex objects (that can't be serialized by default)
- Use more complex validation
- Use the class name as the setting key, letting your IDE tell you when you've got something wrong.

### Set a setting

```php
    \Settings\Setting::setDefaultValue('theme', 'default-two'); // Set the default theme for users
    \Settings\Setting::setDefaultValue('theme', 'my-custom-theme', 2); // User with an ID of `2` sets their own value.
```

