<?php

	namespace LiftKit\Tests\Unit\DependencyInjection\Container;

	use LiftKit\DependencyInjection\Container\Container;
	use LiftKit\Tests\Mock\DependencyInjection\ClassC;
	use LiftKit\Tests\Mock\DependencyInjection\ClassD;
	use PHPUnit_Framework_TestCase;
	use stdClass;

	use LiftKit\Tests\Mock\DependencyInjection\ClassA;
	use LiftKit\Tests\Mock\DependencyInjection\ClassB;


	class ContainerTest extends PHPUnit_Framework_TestCase
	{
		/**
		  * @var Container
		  */
		protected $container;


		public function setUp ()
		{
			$this->container = new Container;
		}


		public function testSetGetParameter ()
		{
			$object = new stdClass();
			$this->container->setParameter('test', $object);

			$this->assertSame($this->container->getParameter('test'), $object);
		}


		public function testSetRule ()
		{
			$this->container->setRule(
				'test',
				function ()
				{
					return new stdClass;
				}
			);

			$object1 = $this->container->getObject('test');
			$object2 = $this->container->getObject('test');

			$this->assertTrue($object1 instanceof stdClass);
			$this->assertTrue($object2 instanceof stdClass);
			$this->assertNotSame($object1, $object2);
		}


		public function testSingleton ()
		{
			$this->container->setRule(
				'test',
				function ()
				{
					return new stdClass;
				},
				true
			);

			$object1 = $this->container->getObject('test');
			$object2 = $this->container->getObject('test');

			$this->assertSame($object1, $object2);
		}


		public function testSingletonRule ()
		{
			$this->container->setSingletonRule(
				'test',
				function ()
				{
					return new stdClass;
				}
			);

			$object1 = $this->container->getObject('test');
			$object2 = $this->container->getObject('test');

			$this->assertSame($object1, $object2);
		}


		/**
		 * @expectedException \LiftKit\DependencyInjection\Exception\Dependency
		 */
		public function testOverrideSingletonFails ()
		{
			$this->container->setSingletonRule(
				'test',
				function ()
				{
					return new stdClass;
				}
			);

			$this->container->getObject('test');

			$this->container->setSingletonRule(
				'test',
				function ()
				{
					return new stdClass;
				}
			);
		}


		public function testOverrideSingleton ()
		{
			$object1 = new stdClass;
			$object2 = new stdClass;

			$this->container->setSingletonRule(
				'test',
				function () use ($object1)
				{
					return $object1;
				}
			);

			$this->container->getObject('test');

			$this->container->setSingletonRule(
				'test',
				function () use ($object2)
				{
					return $object2;
				},
				true
			);

			$this->assertSame(
				$object2,
				$this->container->getObject('test')
			);
		}


		public function testComposedRules ()
		{
			$this->container->setRule(
				'rule1',
				function ()
				{
					return new stdClass;
				},
				true
			);

			$this->container->setRule(
				'rule2',
				function ($container)
				{
					$object = new stdClass;
					$object->innerObject = $container->getObject('rule1');

					return $object;
				}
			);

			$object1 = $this->container->getObject('rule1');
			$object2 = $this->container->getObject('rule2');

			$this->assertSame($object1, $object2->innerObject);
		}


		public function testStoreObject ()
		{
			$object = new stdClass;
			$this->container->storeObject('object', $object);

			$this->assertSame($this->container->getObject('object'), $object);
		}


		public function testBindToClass ()
		{
			$this->container->bindRuleToClass('A', ClassA::class);
			$object = $this->container->getObject('A');

			$this->assertEquals(ClassA::class, get_class($object));
		}


		/**
		 * @expectedException \LiftKit\DependencyInjection\Exception\Dependency
		 */
		public function testBindToClassFails ()
		{
			$this->container->bindRuleToClass('A', 'NonClass');
		}


		public function testAlias ()
		{
			$this->container->bindRuleToClass('A', ClassA::class);
			$this->container->bindClassToAlias(ClassA::class, ClassB::class);

			$object = $this->container->getObject('A');

			$this->assertEquals(ClassB::class, get_class($object));
		}


		public function testAliasDependency ()
		{
			$this->container->bindRuleToClass('C', ClassC::class);

			$object = $this->container->getObject('C');

			$this->assertEquals(ClassC::class, get_class($object));
		}


		/**
		 * @expectedException \LiftKit\DependencyInjection\Exception\Dependency
		 */
		public function testAliasFails ()
		{
			$this->container->bindRuleToClass('A', ClassA::class);
			$this->container->bindClassToAlias(ClassA::class, ClassC::class);
		}


		public function testAliasDependencyRule ()
		{
			$this->container->bindRuleToClass('C', ClassC::class);
			$this->container->bindClassToAlias(ClassA::class, ClassB::class);

			$object = $this->container->getObject('C');

			$this->assertEquals(ClassC::class, get_class($object));
			$this->assertEquals(ClassB::class, get_class($object->getA()));
		}


		public function testAliasDependencyWithRule ()
		{
			$this->container->bindRuleToClass('C', ClassC::class);
			$b = new ClassB;

			$this->container->setRule('A', function () use ($b) {
				return $b;
			});

			$this->container->bindClassToRule(ClassA::class, 'A');

			$object = $this->container->getObject('C');

			$this->assertEquals(ClassC::class, get_class($object));
			$this->assertSame($b, $object->getA());
		}


		/**
		 * @group current
		 */
		public function testWithParameters ()
		{
			$this->container->bindRuleToClass('D', ClassD::class);
			$this->container->bindRuleToClass('C', ClassC::class);
			$b = new ClassB;

			$this->container->setRule('A', function () use ($b) {
				return $b;
			});

			$this->container->bindClassToRule(ClassA::class, 'A');

			$object = $this->container->getObject('D', [$b]);

			$this->assertEquals(ClassD::class, get_class($object));
			$this->assertEquals(ClassC::class, get_class($object->getC()));

			$this->assertSame($b, $object->getA());
			$this->assertSame($b, $object->getC()->getA());
		}
	}