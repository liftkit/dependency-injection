<?php


	namespace LiftKit\DependencyInjection\InstanceStore;


	class InstanceStore
	{
		private $instance;


		public function getInstance ()
		{
			return $this->instance;
		}


		public function storeInstance ($instance)
		{
			$this->instance = $instance;
		}


		public function hasInstance ()
		{
			return isset($this->instance);
		}
	}