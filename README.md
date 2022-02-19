# Laravel Settings
> Powerful and feature rich settings for Laravel.

[![Latest Version](https://img.shields.io/github/v/release/ElbowSpaceUK/laravel-settings?label=Latest%20Version&sort=semver&style=plastic)](https://github.com/ElbowSpaceUK/laravel-settings/releases)
[![Build](https://github.com/ElbowSpaceUK/laravel-settings/actions/workflows/build-status.yml/badge.svg?branch=develop)](https://github.com/ElbowSpaceUK/laravel-settings/actions/workflows/build-status.yml)

## Contents

* [About the Project](#about)
* [Documentation](#documentation)
* [Contributing](#contributing)
* [Copyright and Licence](#copyright-and-licence)
* [Contact](#contact)

## About

Laravel Settings lets you persist strongly typed settings within your app, with support for
- Validation, encryption and authorization controls provided.
- Global settings and user-set settings.
- Native integration with Vue JS.

## Example

```php
\Settings\Setting::setValue('dark_mode', true);
echo \Settings\Setting::getValue('dark_mode'); // true
```

```vue
<template>
    <div :class="{'dark-mode': $setting.dark_mode}"></div>
    <button @click="toggleDarkMode">Toggle</button>
</template>
<script>
    export default {
        methods: {
            toggleDarkMode() {
                this.$setting.dark_mode = !this.$setting.dark_mode;
            }
        }
    }
</script>
```

## Documentation

We've taken care over documenting everything you'll need to get started and use Laravel settings fully.

[Check out the docs](https://elbowspaceuk.github.io/laravel-settings) on our documentation site.

[comment]: <> (To build them locally, you'll need to have ruby &#40;we'd recommend using rbenv&#41; and the gem bundler &#40;https://bundler.io/&#41; installed. Run `bundle install && bundle exec jekyll serve` in the docs folder.)

## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

## Copyright and Licence

This package is copyright © [Toby Twigger](https://github.com/tobytwigger)
and licensed for use under the terms of the MIT License (MIT). Please see
[LICENCE.md](LICENCE.md) for more information.

## Contact

For any questions, suggestions, security vulnerabilities or help, open an issue or email me directly at [tobytwigger1@gmail.com](mailto:tobytwigger1@gmail.com)
