<?php

	namespace LiftKit\DependencyInjection\Container;


	use LiftKit\DependencyInjection\Exception\Dependency as DependencyException;
	use LiftKit\DependencyInjection\Rule\CallbackRule;
	use LiftKit\DependencyInjection\Rule\Rule;
	use LiftKit\DependencyInjection\Rule\SingletonRule;
	use LiftKit\DependencyInjection\Rule\StoredObjectRule;


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
			if (!isset($this->parameters[$identifier])) {
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

			$this->rules[$identifier] = new SingletonRule($this, $rule);

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
	}