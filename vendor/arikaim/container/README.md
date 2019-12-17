<p align="center">
    <img src="https://img.shields.io/github/release/arikaim/container.svg" alt="version: 1.0.0">
    <img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="license: MIT">
</p>

## Arikaim Container
Mini Dependency Injection Container

PSR-11 compatibility

## Installation
> composer require arikaim/container

## Usage 

```php 

use Arikaim\Container\Container;

$container = new Container();

```

**Add service to container**

```php 

$container['service'] = function() {
    echo "Service example";
};

$container->add('service_add',function() {
    echo "Service add example";
});


$container->add('date',function () {
    return new \DateTime();
});


$date = $container['date'];
echo $date->format('Y-m-d');

```
**Add parameters** 

```php

$container['config'] = "Config value";

```

**Replace service**  

```php

$container->replace('service',function() {
     echo "Replace Service";
});

```

**Psr-11** compatibility implement the PSR-11 ContainerInterface

```php
$servcie = $container->get('service');

if ($container->has('service')) {
    \\ Service exists
}

```

## License 
MIT License