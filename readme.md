#Luminark URL Package

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
