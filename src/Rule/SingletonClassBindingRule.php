<?php


	namespace LiftKit\DependencyInjection\Rule;

	use LiftKit\DependencyInjection\Container\Container;
	use LiftKit\DependencyInjection\ClassIndex\ClassIndex;
	use LiftKit\DependencyInjection\InstanceStore\InstanceStore;


	class SingletonClassBindingRule extends ClassBindingRule
	{
		protected $instanceStore;



		public function __construct (Container $container, ClassIndex $classIndex, $className)
		{
			parent::__construct($container, $classIndex, $className);

			$this->instanceStore = new InstanceStore;
		}


		public function resolve (array $params = array())
		{
			if ($this->instanceStore->hasInstance()) {
				return $this->instanceStore->getInstance();

			} else {
				$instance = parent::resolve($params);
				$this->instanceStore->storeInstance($instance);

				return $instance;
			}
		}
	}