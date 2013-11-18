Bowl, Yet Another Dependency Injection Container (PHP5.4+)
==========================================================

Bowl
  - Manages dependencies between objects
  - Performs a Lazy instantiation
  - Performs a Factory pattern
  - Uses no external files to configure dependencies

You
  - Don't have to instantiate object yourself
  - Can avoid Singleton/Factory pattern from your classes

Installation
------------

Create or update your composer.json and run `composer update`

``` json
{
    "require": {
        "kzykhys/bowl": "dev-master"
    }
}
```

Usage
-----

### Define parameters

``` php
$bowl = new \Bowl\Bowl();
$bowl['lang'] = 'en';
$bowl['debug'] = true;
```

### Define a shared service

``` php
$bowl = new \Bowl\Bowl();

$bowl->share('service_name', function () {
    return new stdClass();
});

var_dump($bowl->get('service_name') === $bowl->get('service_name'));
// bool(true)
```

### Define a factory service

``` php
$bowl = new \Bowl\Bowl();

$bowl->factory('service_name', function () {
    return new stdClass();
});

var_dump($bowl->get('service_name') === $bowl->get('service_name'));
// bool(false)
```

### Define a service depending on other services

``` php
$bowl = new \Bowl\Bowl();

$bowl->share('driver.mysql', function () {
    return new MysqlDriver();
});

$bowl->share('connection', function () {
    $c = new Connection();
    $c->setDriver($this->get('driver.mysql'));

    return $c;
});
```

### Using tags to manage collection of services

``` php
$bowl = new \Bowl\Bowl();

$bowl->share('form.type.text', function () {
    return new TextType();
}, ['form.type']);

$bowl->share('form.type.email', function () {
    return new EmailType();
}, ['form.type']);

$bowl->share('form', function () {
    $form = new Form();

    foreach ($this->getTaggedServices('form.type') as $service) {
        $form->addType($service);
    }

    return $form;
});
```

### Real life example

``` php
<?php

require __DIR__ . '/../vendor/autoload.php';

$bowl = new \Bowl\Bowl();

// Set a parameter
$bowl['debug'] = false;

// Shared service
$bowl->share('ciconia.renderer', function () {
    return new \Ciconia\Renderer\HtmlRenderer();
});

// Tagged service
$bowl->share('ciconia.extension.table', function () {
    return new \Ciconia\Extension\Gfm\TableExtension();
}, ['ciconia.extension']);

// This example shows how to manage services using tags
$bowl->share('ciconia', function () {
    // $bowl is bind to this closure, so you can access $this as Bowl.
    if ($this['debug']) {
        $ciconia = new \Ciconia\Diagnose\Ciconia();
    } else {
        $ciconia = new \Ciconia\Ciconia();
    }

    // Resolve dependencies
    $ciconia->setRenderer($this->get('ciconia.renderer'));

    // All services tagged as "ciconia.extension"
    foreach ($this->getTaggedServices('ciconia.extension') as $extension) {
        $ciconia->addExtension($extension);
    }

    return $ciconia;
});

// Get the object
$ciconia = $bowl->get('ciconia');
echo $ciconia->render('Markdown is *awesome*');

// Create a new instance even if this is a shared object
$ciconia = $bowl->reset('ciconia')->get('ciconia');
echo $ciconia->render('Markdown is *awesome*');
```

API
---

 Method                                | Description
---------------------------------------|-------------
 share($name, $closure, $tags = [])    |
 factory($name, $closure, $tags = [])  |
 get($name)                            |
 getTaggedServices($name)              |