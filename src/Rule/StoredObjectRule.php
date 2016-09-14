<?php


	namespace LiftKit\DependencyInjection\Rule;

	use LiftKit\DependencyInjection\InstanceStore\InstanceStore;


	class StoredObjectRule extends Rule
	{
		protected $instanceStore;



		public function __construct ($instance)
		{
			$this->instanceStore = new InstanceStore;
			$this->instanceStore->storeInstance($instance);
		}


		public function resolve (array $params = array())
		{
			return $this->instanceStore->getInstance();
		}
	}