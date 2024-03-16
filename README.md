# @framework

An easy-to-use framework for developing web applications.

> [!NOTE]
> While it's possible, this repository is not intended for direct use in a development project. It's recommended that you use this framework alongside our [boilerplate](echtyushi/framework-boilerplate) instead.

## Requirements
- PHP version 7.4
- opcache for PHP 7.4

## Installation
First, navigate to your project and include this repository as a submodule:

    git submodule add https://github.com/echtyushi/framework Framework

## Credentials

Create a folder named `config` in your project directory. Inside this folder, add a file named `app.php`. In `app.php`, define an array that contains properties named `url` and `development_mode`, where `development_mode` is a boolean variable.

### php.ini

For the `php.ini` we need to enable the extension opcache.

1.  Enable opcache extension:

        zend_extension=opcache

2.  Add opcache settings in `php.ini`:

        opcache.enable=1
        opcache.memory_consumption=128
        opcache.interned_strings_buffer=8
        opcache.max_accelerated_files=4000

## Additional Notes

- This application relies on opcache. See [opcache installation](https://www.php.net/manual/en/opcache.installation.php).
