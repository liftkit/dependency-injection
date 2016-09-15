<?php


	namespace LiftKit\Tests\Mock\DependencyInjection;


	class ClassC
	{
		protected $a;


		public function __construct (ClassA $a)
		{
			$this->a = $a;
		}


		public function getA ()
		{
			return $this->a;
		}
	}