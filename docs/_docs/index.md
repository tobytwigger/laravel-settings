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

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```console
composer require twigger/laravel-settings
```

You can publish the configuration file by running 
```console
php artisan vendor:publish --provider="Twigger\Settings\SettingsServiceProvider"
```
