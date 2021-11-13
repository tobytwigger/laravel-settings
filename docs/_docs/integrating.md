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

Using the methods to get setting values as a form in the get settings docs, you can get a Form instance of the settings you want to show. These will be in groups, depending on the group defined in the setting. Pass this schema to the frontend and render it using a dynamic form generator.

If you want to give the groups more information, such as a proper title and description, you may set it in config.

```php
<?php

// config/settings.php

return [

    'groups' => [
        'security' [
            'title' => 'Security',
            'subtitle' => 'Settings related to the security of the site'
        ],
        ...
    ]
]

]
```
