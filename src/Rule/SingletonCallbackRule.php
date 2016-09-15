<?php


	namespace LiftKit\DependencyInjection\Rule;

	use LiftKit\DependencyInjection\Container\Container;
	use LiftKit\DependencyInjection\InstanceStore\InstanceStore;


	class SingletonCallbackRule extends CallbackRule
	{
		protected $instanceStore;



		public function __construct (Container $container, callable $callback)
		{
			parent::__construct($container, $callback);

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