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

- Quick to set up and use, but powerful enough to scale as your app does.
- Supports anonymous and class-based keys.
- Supports encryption and storing non-primitive values.
- User and global settings provided by default.
- Can add custom types such as a team or organisation.

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

To make use of static analysis and IDE typehinting support, and to help you manage the defined settings, you can use class-based settings.

### Set a setting

```php
    \Settings\Setting::setDefaultValue('theme', 'default-two'); // Set the default theme for users
    \Settings\Setting::setDefaultValue('theme', 'my-custom-theme', 2); // User with an ID of `2` sets their own value.
```

### Read more

Read on to learn more about creating, getting and setting settings.
