---
layout: docs
title: Get Settings
nav_order: 4
---

# Get Settings
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

## Getting a setting value

The easiest way to get the value of a setting is by referencing the setting class directly, e.g. `\Acme\Setting\SiteName::getValue()`.

You can also use
- The facade: `\Settings\Setting::getValue(\Acme\Setting\SiteName::class)`
- The helper: `settings(\Settings\Setting::class)`

To access anonymous settings with their key, you can replace the class name with the key
- The facade: `\Settings\Setting::getValue('siteName')`
- The helper: `settings('siteName')`

### Getting values for types

For setting types like users and teams, where the setting value depends on the session, you can use the same function. This will automatically resolve the current user/team/model from the session and use that. If you do pass an ID in as the first parameter though, it will get the setting value for the given model instead.

```php
\Settings\Setting::getValue('theme') // Get the value for the current user, or the default if no-one is logged in
\Settings\Setting::getValue('theme', 5) // Get the value for user #5, even if someone else is logged in
```

### Aliases

For common settings, you can alias the getters to a single function. Rather than using `\Settings\Setting::getValue(\Acme\Setting\SiteName::class)`, you can use `\Settings\Setting::getSiteName()`.

By doing this you won't get IDE typehinting, but it is a more concise way to refer to settings.

To alias a setting like this, add it to the config file

```php
<?php

return [
    'aliases' => [
        'siteName' => \Acme\Setting\SiteName::class,
        ...
    ]
];
```

You can also add it directly in your service provider `boot` method

```php
public function boot()
{
    \SettingsSetting::alias('siteName', \Acme\Setting\SiteName::class);
}
```

Aliasing in this way also makes your class-based settings accessible through the alias, so site name can now be accessed with `settings(\Acme\Setting\SiteName::class)` or `settings('siteName')`.

## Multi-tenancy

To support multi tenancy, you can set a tenant during the boot of your app. This will usually be an ID, but could be any unique string.

When set, each tenant has their own settings and only their settings are queried.

### Setting the tenant

In the boot method of your service provider, you should add

```php
\Settings\Setting::resolveTenantKeyUsing(function(): ?string {
    // Get the tenant key
});
```

In this closure, you can resolve the tenant from the route/session/anywhere else, and return a string unique to that tenant (such as their ID as a string, or some other unique key). If you return null, the default tenant will be used, which can be useful for public, non-tenanted parts of your site.