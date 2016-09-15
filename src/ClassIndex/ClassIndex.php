<?php


	namespace LiftKit\DependencyInjection\ClassIndex;


	use LiftKit\DependencyInjection\Exception\Dependency;


	class ClassIndex
	{


		/**
		 * @var array
		 */
		private $classToRuleIndex = array();


		/**
		 * @var array
		 */
		private $ruleToClassIndex = array();


		/**
		 * @var array
		 */
		private $classToAliasIndex = array();


		/**
		 * @var array
		 */
		private $aliasToClassIndex = array();


		public function setRuleToClass ($identifier, $className)
		{
			if (! is_string($identifier)) {
				throw new Dependency('The rule identifier must be a string.');
			}

			if (! is_string($className)) {
				throw new Dependency('The classname must be a string.');
			}

			if (! class_exists($className)) {
				throw new Dependency('Invalid classname: ' . $className);
			}

			$this->classToRuleIndex[$className]  = $identifier;
			$this->ruleToClassIndex[$identifier] = $className;

			return $this;
		}


		public function setClassToRule ($identifier, $className)
		{
			if (! is_string($identifier)) {
				throw new Dependency('The rule identifier must be a string.');
			}

			if (! is_string($className)) {
				throw new Dependency('The classname must be a string.');
			}

			if (! class_exists($className)) {
				throw new Dependency('Invalid classname: ' . $className);
			}

			$this->classToRuleIndex[$className]  = $identifier;

			return $this;
		}


		public function setClassToAlias ($className, $resolvedClassName)
		{
			if (! is_subclass_of($resolvedClassName, $className)) {
				throw new Dependency($resolvedClassName . ' must be a subclass of ' . $className);
			}

			$this->classToAliasIndex[$className]         = $resolvedClassName;
			$this->aliasToClassIndex[$resolvedClassName] = $className;

			return $this;
		}


		public function resolveClassToRule ($className)
		{
			if (! is_string($className)) {
				throw new Dependency('The classname must be a string.');
			}

			$className = $this->resolveAliasToClass($className);

			return $this->classToRuleIndex[$className];
		}


		public function resolveAliasToClass ($className)
		{
			if (! is_string($className)) {
				throw new Dependency('The classname must be a string.');
			}

			if (isset($this->aliasToClassIndex[$className])) {
				return $this->aliasToClassIndex[$className];
			} else {
				return $className;
			}
		}


		public function resolveClassToAlias ($className)
		{
			if (! is_string($className)) {
				throw new Dependency('The classname must be a string.');
			}

			if (isset($this->classToAliasIndex[$className])) {
				return $this->classToAliasIndex[$className];
			} else {
				return $className;
			}
		}
	}