# Dependency Injection

A simple dependency injection library

## Create a new container

```php
use LiftKit\DependencyInjection\Container\Container;

$container = new Container;
```

## Rules

A rule is an anonymous function that defines how to create an object.

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

## Automatic resolution

The container can also bind a rule to a class. An instance of a class
can be created automatially by looking at the type hints of each
constructor argument. In the case below, and instance of `B` is 
automatically created before creating an instance of `A`. The newly-
created instance of B is then injected into the constructor of `A`.

```php
class A
{
  private $b;
  
  public function __construct (B $b)
  {
    $this->b = $b;
  }
  
  public function getB ()
  {
    return $this->b;
  }
}

class B
{
  // placeholder class
}

$container->bindRuleToClass(
  'GiveMeANewA',
  A::class
);

$a = $container->getObject('GiveMeANewA');
$b = $a->getB();

// $a is a new instance of A. $b is a new instance of b.
```

## Automatic resolution with rules

In some cases, you may need to tell the injector to create an instance following a different rule when it encounters the
typehint of a certain class instead. In the example below, a rule is created for the construction of `B`. When the container realizes it needs an instance of `B` when creating an `A`, it will follow that rule to create `B` first. In this case, `A`'s constructor was injected with an instance of B created by the rule `'GiveMeANewB'` to create `$a`.

```php
$container->setRule(
  'GiveMeANewB',
  function ()
  {
    $b = new B;
    
    $b->createdByRule = true;
    
    return $b;
  }
);

$container->bindClassToRule(
  B::class,
  'GiveMeANewB'
);

$a = $container->getObject('GiveMeANewA');
$b = $a->getB();

// $b->createdByRule is true
```

## Automatic resolution with parameters

Sometimes, there are additional parameters that need to be passed to the constructor of a new instance that is being automatically constructed. Below the variables `$param1` and `$param2` will be injectied into `C`'s constructor, while `B` will be created by the rule `'GiveMeANewB'` above. Any additional parameters must call at the end of the constructor's list of parameters.

```php
class C
{
  private $b;
  private $param1;
  private $param2;
  
  public function __construct (B $b, $param1, $param2)
  {
    $this->b = $b;
    $this->param1 = $param1;
    $this->param2 = $param2;
  }
  
  public function getB ()
  {
    return $this->b;
  }
  
  public function getParam1 ()
  {
    return $this->param1;
  }
  
  public function getParam2 ()
  {
    return $this->param2;
  }
}

$container->bindRuleToClass(
  'GiveMeANewC',
  C::class
);

$param1 = 1;
$param2 = 2;

$c = $container->getObject(
  'GiveMeANewC',
  [
    $param1,
    $param2,
  ]
);

$b = $c->getB();
$cParam1 = $c->getParam1();
$cParam2 = $c->getParam2();

// $b is an instance of B
// $cParam1 is 1
// $cParam2 is 2
```

## Binding classes to aliases

Sometimes, you may want the container to resolve to a subclass when it encounters a particular typehint. In the example below, a new instance of `D` will be injected into `A`, instead of an instance of `B`.

```php
class D extends B
{
  // placeholder class
}

$container->bindClassToAlias(
  B::class,
  D::class
);

$a = $container->getObject('GiveMeANewA');
$d = $a->getB();

// $d is an instance of D
```
