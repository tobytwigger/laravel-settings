---
layout: docs
title: Set Settings
nav_order: 5
---

# Set Settings
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

## Set a setting value

To set a setting, you can use the `setValue` method, such as `\Settings\Setting::setValue('siteName', 'New site name')`. This will set the setting value, or throw a validation exception if there was a problem with the value.

To set the default value of any setting, which is returned if the setting does not yet have a value, you can use `\Settings\Setting::setDefaultValue('siteName', 'Default Site Name')`;

To use class-based settings, just pass in the class in place of the key. If you want to use the setting directly, you can access all set and get functions through the setting `\Acme\Setting\SiteName::setValue('New Site Name')`.

### Setting types

Most setting types (such as user and team settings) have a value that depends on the current session. For these settings, you may pass
in the ID of the user/team/model as a third parameter.

`\Settings\Setting::setValue('enable2FA', true, 5)` will enable 2FA for the team with an ID of 5.

For the settings that depend on a model like this, if you don't pass an ID in as the second parameter it will be automatically resolved from the session.

To update the default, which will affect any users that haven't changed their settings yet, you can use `\Settings\Setting::setDefaultValue('enable2FA', true)` to enable 2FA by default.

As normal, you can use class-based settings directly and remove the key argument, such as `\Acme\Setting\SiteName::setDefaultValue('New Site')`.

## Multi-tenancy

When using multi-tenancy tools to provide settings to multiple tenants, setting the value of a setting as normal will always set it for the current tenant.

You can set the default value for all tenants by using `\Settings\Setting::withoutTenant()->setDefaultValue(\Acme\Setting\SiteName::class, 'Default Site Name')`. Any tenant who has not set the setting will get 'Default Site Name' as a response. 

If this is used without `withoutTenant()`, it will set the default value for the current tenant.
