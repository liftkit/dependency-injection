<?php


	namespace LiftKit\Tests\Mock\DependencyInjection;


	class ClassD
	{
		protected $c;
		protected $a;


		public function __construct (ClassC $c, ClassA $a)
		{
			$this->c = $c;
			$this->a = $a;
		}


		public function getC ()
		{
			return $this->c;
		}


		public function getA ()
		{
			return $this->a;
		}
	}