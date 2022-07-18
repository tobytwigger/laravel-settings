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

- Quick to get started.
- Validation, encryption and authorization controls provided.
- Global settings and user-set settings.
- Native integration with Vue JS.
- Auto generate UI for managing settings.

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```console
composer require tobytwigger/laravel-settings
```

You can publish the configuration file by running

```console
php artisan vendor:publish --provider="Settings\SettingsServiceProvider"
```

This will publish the configuration file and migrations.

## Basic Usage

You can create a new setting in the `boot` method of any service provider.

```php
    public function boot()
    {
        \Settings\Setting::createGlobal(
            key: 'siteName',
            defaultValue: 'My App'
        );
    }
```

This setting can then be accessed anywhere in your Laravel application

```php
    echo \Settings\Setting::getValue('siteName') // My App
```

or updated to a new value

```php
    \Settings\Setting::setValue('siteName', 'My New App');
```
