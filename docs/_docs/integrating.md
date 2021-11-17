---
layout: docs
title: Integrate
nav_order: 6
---

# Basic Usage
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
7
{: .label .label-red }
This page currently documents features that haven't yet been created.

## API

This package comes with an API for updating the settings. 

### Get a setting value

#### Request

```http
GET /_setting/{key} HTTP/1.1
Accept: application/json
Content-Type: application/json
```

#### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json
{
   "setting": {
        key: '',
        value: '',
        updated_at: ''
    }
}
```

### Set a setting value(s)

**POST** /setting
[
    {"value": "setting-value"}
]

#### Request

```http
POST /_setting HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "settings": {
        "key1": "First setting",
        "key2": "Second setting"
    ]
}
```

#### Response

```http
HTTP/1.1 204 No Content
Content-Type: application/json
```

### Get all setting values

#### Request

```http
GET /_setting HTTP/1.1
Accept: application/json
Content-Type: application/json
```

#### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json
{
   "settings": [
        {
            key: '',
            value: '',
            updated_at: ''
        },
        {...},
    ]
}
```

## Validation

If you use your own API to update settings, or a standard web request, you can use the following validation rule to check all the given settings are valid according to their validation rules

- `settings` - the given attribute must be an array of settings as key value pairs

## Creating a settings page

We've previously covered getting a Form instance from settings/groups of settings. Pass this schema to the frontend and render it using a dynamic form generator. Each setting group will appear in a different form group.

Integrating with Form schema generator

## Transition from keys to class-based

You can alias the old anonymous setting key to the new class-based name

```php
    \Settings\Setting::alias(\Acme\Settings\SiteName::class, 'siteName');
```

Now, when you call `\Acme\Settings\SiteName::getValue()`, the class will automatically be replaced with the alias. This means there should be no friction when changing over to class-based settings, since both options are viable. 

### Removing the anonymous setting

How to then fully move over

## Using settings in service providers

It can be tempting to use a setting in your service provider, to store things like service credentials that are needed to set it up.

However, the settings package isn't ready to use until the application is fully booted, since it depends on things like cache, encryption and its own bindings to function. If you try to use it before the application is booted, you'll get an exception.

To work around this, so you can use settings to set up services needed by the rest of your app, you can use the `booted` callback on the application. In the `register` function of your service provider, define a callback which should be called after the application is booted. This will be called as soon as the application is booted, meaning you can do any service setup here and make use of the settings.

The easier option is to register your bindings as a callback, which should only be called once the service is actually requested. But either option is equally fine and you can use whichever suits the situation best!
