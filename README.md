# Dependency Injection

A simple dependency injection library

## Create a new container

```php
use LiftKit\DependencyInjection\Container\Container;

$container = new Container;
```

## Rules

A rule is an anonymouse function that defines how to create an object.

```php
$container->setRule(
  'SomeRule',
  function ()
  {
    return new SomeClass();
  }
);

$someObject = $container->getObject('SomeRule');

// $someObject will be an instance of SomeClass
```

## Singleton rules

A rule will execute each time `getObject` is called by default. In order to force it to 
execute only once, we use `setSingletonRule`. Each call to `getObject` for the rule will return
the same object.

```php
$container->setSingletonRule(
  'SomeSingletonRule',
  function ()
  {
    return new SomeClass();
  }
);

$object1 = $container->getObject('SomeSingletonRule');
$object2 = $container->getObject('SomeSingletonRule');

// $object1 and $object2 are the same object
```

## Rules with parameters

Some rules can have parameters passed to them. The first argument to the `setRule` callback is the 
container istelf. Each subsequent argument is supplied by an optional array of parameters supplied
to `getObject`.

```php
$container->setRule(
  'SomeRuleWithParameters',
  function (Container $container, $arg1, $arg2)
  {
    return new SomeClass($arg1, $arg2);
  }
);

$someObject = $container->getObject('SomeRuleWithParameters', ['arg1', 'arg2']);

// SomeClass will be contructed with 'arg1' and 'arg2' as the parameters to is constructor.
```

## Rules that reference other rules

```php
$container->setRule(
  'Rule1',
  function ()
  {
    return new SomeClass;
  }
);

$container->setRule(
  'Rule2',
  function (Container $container)
  {
    return new OtherClass($container->getObject('Rule1'));
  }
);

$someObject = $container->getObject('Rule2');

// $someObject will be an instance of OtherClass with a new instance of SomeClass injected as its
// first contructor argument.
```

## Overriding rules

Rules can be overridden by redefining them. This is useful for modular code.

```php
$conatiner->setRule(
  'SomeRule',
  function ()
  {
    return new SomeClass;
  }
);

$container->setRule(
  'SomeRule',
  function ()
  {
    return new OtherClass;
  }
);

$someObject = $container->getObject('SomeRule');

// $someobject will be an instance of OtherClass
```

## Storing an instance

You can also store an object you've already created an bind it to a rule.

```php
$someObject = new SomeObject;

$container->storeObject('SomeRule', $someObject);

$otherObject = $container->getObject('SomeRule');

// $someObject and $otherObject are the some object
```
