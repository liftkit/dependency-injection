<?php


	namespace LiftKit\Tests\Mock\DependencyInjection;


	class ClassE
	{
		protected $b;
		protected $a;


		public function __construct (ClassA $a, ClassB $b)
		{
			$this->b = $b;
			$this->a = $a;
		}


		public function getB ()
		{
			return $this->b;
		}


		public function getA ()
		{
			return $this->a;
		}
	}