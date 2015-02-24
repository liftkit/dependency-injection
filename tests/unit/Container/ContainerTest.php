<?php

	namespace LiftKit\Tests\Unit\DependencyInjection\Container;
	
	use LiftKit\DependencyInjection\Container\Container;
	use PHPUnit_Framework_TestCase;
	use stdClass;
	
	
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
		
		
		public function testComposedSingleton ()
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
		
		
		public function assertComposedRules ()
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
	}