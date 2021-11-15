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

To set a setting, you can use the `setValue` method on the setting, such as `Acme\Setting\SiteName::setValue('New site name')`. This will set the setting value, or throw a validation exception if there was a problem with the value.

To set the default value of any setting, which is returned if the setting does not yet have a value, you can use `Acme\Setting\SiteName::setDefaultValue('Default site name')`. Or use the facade with `\Settings\Setting::setDefaultValue(\Acme\Setting\SiteName::class, 'Default Site Name')`;

### Setting types

Most setting types (such as user and team settings) have a value that depends on the current session. For these settings, you may pass
in the ID of the user/team/model as a second parameter.

`\Acme\Setting\Team2FAEnabled::setValue(true, 5)` will enable 2FA for the team with an ID of 5.

For the settings that depend on a model like this, if you don't pass an ID in as the second parameter it will be automatically resolved from the session.

To update the default, which will affect any users that haven't changed their settings yet, you can use `\Acme\Setting\Team2FAEnabled::setDefaultValue(true)` to enable 2FA by default.

## Multi-tenancy

When using multi-tenancy tools to provide settings to multiple tenants, setting the value of a setting as normal will always set it for the current tenant.

You can set the default value for all tenants by using `\Settings\Setting::withoutTenant()->setDefaultValue(\Acme\Setting\SiteName::class, 'Default Site Name')`. Any tenant who has not set the setting will get 'Default Site Name' as a response. 

If this is used without `withoutTenant()`, it will set the default value for the current tenant.
