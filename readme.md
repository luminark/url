#Luminark URL Package

[![Build Status](https://img.shields.io/travis/luminark/url.svg?style=flat-square)](https://travis-ci.org/luminark/url)
[![Code Coverage](https://img.shields.io/codecov/c/github/luminark/url.svg?style=flat-square)](https://codecov.io/github/luminark/url)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/a4587eb2-b09b-4259-9a19-3e39a11d75ab.svg?style=flat-square)](https://insight.sensiolabs.com/projects/a4587eb2-b09b-4259-9a19-3e39a11d75ab)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/luminark/url.svg)](https://scrutinizer-ci.com/g/luminark/url)

##Installation

```php
composer require luminark/url
```

Add service provider to your `config/app.php`:

```php
Luminark/Url/UrlServiceProvider::class,
```

##Resource Model

Have the Eloquent model URLs will be pointing to use the `Luminark/Url/Traits/HasUrlTrait` trait and implement the `Luminark/Url/Interfaces/HasUrlInterface` interface.

##Resource Controller

The controller which will be handling URLs needs to use the `Luminark/Url/Traits/HandlesUrlTrait` trait and extend the `getUrlResourceResponse(Url $url)` method which receives the requested `Url` model.

In your `routes.php` (at the bottom) add a wildcard route handler which uses `getUrlResource` method. E.g.:

```php
Route::get('{uri?}', ['uses' => 'UrlController@getUrlResource'])->where('uri', '.*');
```
