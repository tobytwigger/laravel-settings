---
layout: docs 
title: Advanced
nav_order: 4
has_children: true
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


## Using settings in service providers

It can be tempting to use a setting in your service provider, to store things like service credentials that are needed to set it up.

However, the settings package isn't ready to use until the application is fully booted, since it depends on things like cache, encryption and its own bindings to function. If you try to use it before the application is booted, you'll get an exception.

To work around this, so you can use settings to set up services needed by the rest of your app, you can use the `booted` callback on the application. In the `register` function of your service provider, define a callback which should be called after the application is booted. This will be called as soon as the application is booted, meaning you can do any service setup here and make use of the settings.

The easier option is to register your bindings as a callback, which should only be called once the service is actually requested. But either option is equally fine and you can use whichever suits the situation best!

## Setting Types

Learn how to create a custom setting type in the [setting type documentation](setting-types).
