Laravel Explicit Array
======================

![CI](https://github.com/renoki-co/laravel-explicit-array/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/laravel-explicit-array/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/laravel-explicit-array/branch/master)
[![StyleCI](https://github.styleci.io/repos/421948177/shield?branch=master)](https://github.styleci.io/repos/421948177)
[![Latest Stable Version](https://poser.pugx.org/renoki-co/laravel-explicit-array/v/stable)](https://packagist.org/packages/renoki-co/laravel-explicit-array)
[![Total Downloads](https://poser.pugx.org/renoki-co/laravel-explicit-array/downloads)](https://packagist.org/packages/renoki-co/laravel-explicit-array)
[![Monthly Downloads](https://poser.pugx.org/renoki-co/laravel-explicit-array/d/monthly)](https://packagist.org/packages/renoki-co/laravel-explicit-array)
[![License](https://poser.pugx.org/renoki-co/laravel-explicit-array/license)](https://packagist.org/packages/renoki-co/laravel-explicit-array)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## 🤝 Supporting

If you are using one or more Renoki Co. open-source packages in your production apps, in presentation demos, hobby projects, school projects or so, spread some kind words about our work or sponsor our work via Patreon. 📦

You will sometimes get exclusive content on tips about Laravel, AWS or Kubernetes on Patreon and some early-access to projects or packages.

[<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" height="41" width="175" />](https://www.patreon.com/bePatron?u=10965171)

## 🚀 Installation

You can install the package via composer:

```bash
composer require renoki-co/laravel-explicit-array
```

## 🙌 Usage

The original Laravel's `Arr::set()` method treats the dots within the key as separators for nested values. This is expected. The segments will create a nested value `some -> annotation -> com/ttl` with a value of `1800`.

```php
$annotations = [
    'some.annotation.com/ttl' => 900,
];

Arr::set($annotations, 'some.annotation.com/ttl', 1800);

// Current result
// [
//     'some' => [
//         'annotation' => [
//             'com/ttl' => 1800
//         ]
//     ]
// ]

// Desired result
// [
//     'some.annotation.com/ttl' => 1800
// ]
```

To fix this, Explicit Array introduces a new `RenokiCo\ExplicitArray\Arr` class, which altered the `::set()` method, so that will make sure to read the segments between quotes as literal keys.

**You may use this class as your regular `Arr` class because it extends the original `\Illuminate\Support\Arr` class.**

```php
use RenokiCo\ExplicitArray\Arr;

Arr::set($annotations, '"some.annotation.com/ttl"', 1800);

// [
//     'some.annotation.com/ttl' => 1800
// ]
```

This can work with mixed segments, meaning that as long as you keep the dots outside the quotes, you can specify nested values:

```php
use RenokiCo\ExplicitArray\Arr;

Arr::set($annotations, 'annotations.nested."some.annotation.com/ttl"', 1800);

// [
//     'annotations' => [
//         'nested' => [
//             'some.annotation.com/ttl' => 1800
//         ]
//     ]
// ]
```

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
