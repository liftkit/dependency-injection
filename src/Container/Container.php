<?php

	namespace LiftKit\DependencyInjection\Container;


	use LiftKit\DependencyInjection\Exception\Dependency as DependencyException;
	use LiftKit\DependencyInjection\Exception\Dependency;
	use LiftKit\DependencyInjection\Rule\CallbackRule;
	use LiftKit\DependencyInjection\Rule\ClassBindingRule;
	use LiftKit\DependencyInjection\Rule\SingletonClassBindingRule;
	use LiftKit\DependencyInjection\Rule\Rule;
	use LiftKit\DependencyInjection\Rule\SingletonCallbackRule;
	use LiftKit\DependencyInjection\Rule\StoredObjectRule;
	use LiftKit\DependencyInjection\ClassIndex\ClassIndex;


	/**
	 * Class Base
	 *
	 * @package LiftKit\DependencyInjection\Container
	 */
	class Container
	{
		/**
		 * @var Rule[]
		 */

		protected $rules = array();


		/**
		 * @var array
		 */

		protected $parameters = array();


		/**
		 * @var ClassIndex
		 */
		protected $classIndex;


		public function __construct (ClassIndex $classIndex = null)
		{
			if ($classIndex) {
				$this->classIndex = $classIndex;
			} else {
				$this->classIndex = new ClassIndex;
			}
		}


		/**
		 * @param string $identifier
		 * @param mixed  $value
		 *
		 * @returns self
		 */
		public function setParameter ($identifier, $value)
		{
			$this->parameters[$identifier] = $value;

			return $this;
		}


		/**
		 * @param string $identifier
		 *
		 * @return mixed
		 * @throws DependencyException
		 */
		public function getParameter ($identifier)
		{
			if (! isset($this->parameters[$identifier])) {
				throw new DependencyException('Unknown parameter ' . var_export($identifier, true) . '.');
			}

			return $this->parameters[$identifier];
		}


		/**
		 * @param string   $identifier
		 * @param callable $rule
		 * @param bool     $singleton
		 *
		 * @returns self
		 * @throws DependencyException
		 */
		public function setRule ($identifier, callable $rule, $singleton = false)
		{
			if (! is_bool($singleton)) {
				throw new DependencyException('The singleton parameter must be boolean.');
			}

			if ($singleton) {
				$this->setSingletonRule($identifier, $rule);
			} else {
				$this->rules[$identifier] = new CallbackRule($this, $rule);
			}

			return $this;
		}


		/**
		 * @param string   $identifier
		 * @param callable $rule
		 *
		 * @returns self
		 * @throws DependencyException
		 */
		public function setSingletonRule ($identifier, $rule, $force = false)
		{
			if (! $force && isset($this->rules[$identifier])) {
				throw new DependencyException('Attempt to override singleton rule ' . $identifier);
			}

			$this->rules[$identifier] = new SingletonCallbackRule($this, $rule);

			return $this;
		}


		/**
		 * @param string $identifier
		 * @param object $object
		 *
		 * @throws DependencyException
		 */
		public function storeObject ($identifier, $object)
		{
			if (! is_object($object)) {
				throw new DependencyException('You must pass an object to storeObject');
			}

			$this->rules[$identifier] = new StoredObjectRule($object);

			return $this;
		}


		/**
		 * @param string $identifier
		 * @param array  $parameters
		 *
		 * @return mixed
		 * @throws DependencyException
		 */
		public function getObject ($identifier, array $parameters = array())
		{
			if (! isset($this->rules[$identifier])) {
				throw new DependencyException('Unknown object or rule ' . var_export($identifier, true) . '.');
			}

			$rule = $this->rules[$identifier];

			return $rule->resolve($parameters);
		}


		/**
		 * @param $identifier
		 * @param $className
		 *
		 * @return $this
		 */
		public function bindRuleToClass ($identifier, $className)
		{
			$this->classIndex->setRuleToClass($identifier, $className);

			$this->rules[$identifier] = new ClassBindingRule($this, $this->classIndex, $className);

			return $this;
		}


		/**
		 * @param $identifier
		 * @param $className
		 *
		 * @return $this
		 */
		public function bindSingletonRuleToClass ($identifier, $className)
		{
			$this->classIndex->setRuleToClass($identifier, $className);

			$this->rules[$identifier] = new SingletonClassBindingRule($this, $this->classIndex, $className);

			return $this;
		}


		/**
		 * @param $identifier
		 * @param $className
		 *
		 * @return $this
		 */
		public function bindClassToRule ($className, $identifier)
		{
			if (! isset($this->rules[$identifier])) {
				throw new Dependency('Class ' . $className . ' set into invalid rule ' . $identifier);
			}

			$this->classIndex->setClassToRule($identifier, $className);

			return $this;
		}


		/**
		 * @param $className
		 * @param $resolvedClassName
		 *
		 * @return $this
		 */
		public function bindClassToAlias ($className, $resolvedClassName)
		{
			$this->classIndex->setClassToAlias($className, $resolvedClassName);

			return $this;
		}
	}