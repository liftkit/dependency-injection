<?php


	namespace LiftKit\Tests\Mock\DependencyInjection;


	class ClassG
	{
		protected $a;
		protected $param;


		public function __construct (ClassA $a, $param = true)
		{
			$this->a = $a;
			$this->param = $param;
		}


		public function getA ()
		{
			return $this->a;
		}


		public function getParam ()
		{
			return $this->param;
		}
	}