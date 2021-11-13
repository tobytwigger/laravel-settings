---
layout: docs
title: JS
nav_order: 7
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

## Vue

Since the frontend makes use of the settings too, it's easy to use this package directly from your js.

You will need to install the js package. If using Vue, add the following to your app.js file. Here we use 1 as the model ID, replace with whatever the actual model ID is`

```js
import Settings from '@twigger/settings';

Vue.use(Settings);

```

Then in your Vue app

```vue
<template>
    Setting value: { { $setting.get('key', 1) } }
</template>
<!--...-->
computed: {
    description() {
        return this.$setting.get('key', 1)
    }
}
```



## Setting Keys

On the PHP side, since all settings are class based it's impossible to use the incorrect setting key. To keep this consistency in js, the keys can be retrieved from a json object. This is automatically generated for you. For our site name example (`\Acme\Settings\SiteName`), this looks like

```
    {
        acme: {
            settings: {
                siteName: SettingObject
            }
        }
    }
```

You can access this object with `this.$setting.keys`. Once you have a setting object, you can call the following functions

- `this.$setting.keys.acme.settings.siteName.get(1)` - Get the value for the model with an ID 1
- `...siteName.get()` - Get the default value/global setting value
- `...siteName.set('value', 1)` - Set the value for the model 1
- `...siteName.set('value')` - Set the default/global value

## JS

Using functions directly

```js
import {getSetting, keys} from '@twigger/settings'

getSetting('key', 1)
// keys is an object of keys as above
```
