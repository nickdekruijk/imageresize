[![Latest Stable Version](https://poser.pugx.org/nickdekruijk/imageresize/v/stable)](https://packagist.org/packages/nickdekruijk/imageresize)
[![Latest Unstable Version](https://poser.pugx.org/nickdekruijk/imageresize/v/unstable)](https://packagist.org/packages/nickdekruijk/imageresize)
[![Monthly Downloads](https://poser.pugx.org/nickdekruijk/imageresize/d/monthly)](https://packagist.org/packages/nickdekruijk/imageresize)
[![Total Downloads](https://poser.pugx.org/nickdekruijk/imageresize/downloads)](https://packagist.org/packages/nickdekruijk/imageresize)
[![License](https://poser.pugx.org/nickdekruijk/imageresize/license)](https://packagist.org/packages/nickdekruijk/imageresize)

# ImageResize for Laravel
A simple, yet efficient solution for image resizing and caching with Laravel.
Based on my previous imageresize package, now renamed to [nickdekruijk/imageresize-legacy](https://github.com/nickdekruijk/imageresize-legacy).

## Installation
To install the package use

`composer require nickdekruijk/imageresize`

## Configuration
After installing for the first time publish the config file with

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\ImageResize\ServiceProvider"`

A default config file called `imageresize.php` will be available in your Laravel `/config` folder. See this file for more details.

## How does it work
Let's assume you have an image in `/public/media/images/test.jpg` and a template called `thumbnail`. And have set the imageresize.route config to `media/resized`.

Referring to `http://domain.com/media/resized/thumbnail/images/test.jpg` will trigger the imageresize route in laravel since the file doesn't exist. Imageresize then creates the resized image and saves it as `/public/media/resized/thumbnail/images/test.jpg`.

So the next time you refer to `http://domain.com/media/resized/thumbnail/images/test.jpg` the file does exist and the image is served without triggering any php/laravel code for optimal performance.

## Drawbacks
There is however one disadvantage: if the original image is edited or removed the resized file will still remain the same since referring to it doesn't trigger the imageresize package. You will have to manually delete it or use the `php artisan imageresize:delete` command.
