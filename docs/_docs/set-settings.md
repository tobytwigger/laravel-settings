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

### Setting types

Most setting types (such as user and team settings) have a value that depends on the current session. For these settings, you may pass
in the ID of the user/team/model as a second parameter.

`\Acme\Setting\Team2FAEnabled::setValue(true, 5)` will enable 2FA for the team with an ID of 5.

For the settings that depend on a model like this, if you don't pass an ID in as the second parameter it will be automatically resolved from the session.

To update the default, which will affect any users that haven't changed their settings yet, you can use `\Acme\Setting\Team2FAEnabled::setDefaultValue(true)` to enable 2FA by default.
