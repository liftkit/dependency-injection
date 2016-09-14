<?php


	namespace LiftKit\DependencyInjection\Rule;

	use LiftKit\DependencyInjection\Container\Container;


	class CallbackRule extends Rule
	{
		protected $container;
		protected $callback;



		public function __construct (Container $container, callable $callback)
		{
			$this->container = $container;
			$this->callback = $callback;
		}


		public function resolve (array $params = array())
		{
			array_unshift($params, $this->container);

			return call_user_func_array($this->callback, $params);
		}
	}