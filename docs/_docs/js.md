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

{: .label .label-red }
This page currently documents features that haven't yet been created.

## Vue

Since the frontend makes use of the settings too, it's easy to use this package directly from your js.

You will need to install the js package. If using Vue, add the following to your app.js file. Here we use 1 as the model ID, replace with whatever the actual model ID is.

```js
import Settings from '@twigger/settings';

Vue.use(Settings);

```

Then in your Vue app

```vue
<template>
    Setting value: <span v-text="$setting.get('key', 1)"></span>
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

You can access this object with `this.$setting.keys`. If you don't dig into the object you'll have a list of all settings. If you dig through the namespaces to get a setting object, you can then call the following functions

- `this.$setting.keys.acme.settings.siteName.get(1)` - Get the value for the model with an ID 1
- `this.$setting.keys.acme.settings.siteName.get()` - Get the default value/global setting value
- `this.$setting.keys.acme.settings.siteName.set('value', 1)` - Set the value for the model 1
- `this.$setting.keys.acme.settings.siteName.set('value')` - Set the default/global value

### Aliases

To make accessing these settings easier, the aliases referenced in the configuration will also be applied to these keys. If `\Acme\Settings\SiteName` is aliases to `SiteName`, then you can access the JS key with `this.$settings.aliases.siteName`, where you can then call `.get()` or `.set()` as necessary.

You can also just use `this.$settings.siteName` and omit `aliases`, but please note this may cause issues if your setting name is `aliases` or `keys`, or any other property of `$settings`, so it's usually best to use the `this.$settings.aliases` key directly.

## JS

Using functions directly

```js
import {getSetting, keys} from '@twigger/settings'

getSetting('key', 1)
// keys is an object of keys as above
```
