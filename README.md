[![License](https://poser.pugx.org/sgmendez/json/license.svg)](https://packagist.org/packages/sgmendez/json)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f3e4c839-49ea-4065-8370-0e98758a7062/mini.png)](https://insight.sensiolabs.com/projects/f3e4c839-49ea-4065-8370-0e98758a7062)
[![Latest Stable Version](https://poser.pugx.org/sgmendez/json/v/stable.svg)](https://packagist.org/packages/sgmendez/json) 
[![Total Downloads](https://poser.pugx.org/sgmendez/json/downloads.svg)](https://packagist.org/packages/sgmendez/json) 
[![Latest Unstable Version](https://poser.pugx.org/sgmendez/json/v/unstable.svg)](https://packagist.org/packages/sgmendez/json) 

# Introduction
--------------

This PHP library create a wrapper for `json_encode` and `json_decode` PHP functions
that normalize use across de PHP versions and throw exceptions when encoding or
decoding fail.

# Requirements
--------------

This library require PHP 5.3.3 or higher

# Installation
--------------

You can use [Composer](https://getcomposer.org) to use this library in 
your application.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

```
curl -s http://getcomposer.org/installer | php
```
And then execute this command to add libary to your project:

```
$ composer require sgmendez/json
```
Or require [`sgmendez/json`](http://sgmendez.github.io/json/)
into your `composer.json` file:


``` 
json
{
    "require": {
        "sgmendez/json": "1.0.*"
    }
}
```

# Tests
-------
You can run the PHPUnit tests, in directory test execute:

    $ php phpunit.phar .

In this directory there are a copy of phpunit.phar for execution test

# Examples
----------

Encode JSON data:

```
php
use Sgmendez\Json\Json;

$json = new Json();

try
{
    $arrayData = array('foo' => 'Foo', 'bar' => 'Bar');
    $jsonData = $json->encode($arrayData);
} 
catch (Exception $ex) 
{
    echo '[EXCEPTION] MSG: '.$ex->getMessage().' | FILE: '.$ex->getFile().': '.$ex->getLine()."\n";
}

```

Decode JSON string (by default, return array data):

```
php
use Sgmendez\Json\Json;

$json = new Json();

try
{
    $jsonData = '{"foo":"Foo","bar":"Bar"}';
    $dataArray = $json->decode($jsonData);
} 
catch (Exception $ex) 
{
    echo '[EXCEPTION] MSG: '.$ex->getMessage() .
         ' | FILE: '.$ex->getFile().': '.$ex->getLine()."\n";
}


```

Decode JSON file (by default, return array data):

```
php
use Sgmendez\Json\Json;

$json = new Json();

try
{
    $dataArray = $json->decodeFile('/path/to/file.json');
} 
catch (Exception $ex) 
{
    echo '[EXCEPTION] MSG: '.$ex->getMessage() .
         ' | FILE: '.$ex->getFile().': '.$ex->getLine()."\n";
}

```
If you need to check if is valid json data, you can to use `checkValidJsonData()` method.


# License
---------
Licensed under the BSD License:

   http://opensource.org/licenses/bsd-license.php

