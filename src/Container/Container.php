<?php

	namespace LiftKit\DependencyInjection\Container;


	use LiftKit\DependencyInjection\Exception\Dependency as DependencyException;


	/**
	 * Class Base
	 *
	 * @package LiftKit\DependencyInjection\Container
	 */

	class Container
	{
		/**
		 * @var array
		 */

		protected $rules = array();


		/**
		 * @var array
		 */

		protected $parameters = array();


		/**
		 * @var array
		 */

		protected $instances = array();


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
		public function setRule ($identifier, $rule, $singleton = false)
		{
			if (!is_callable($rule)) {
				throw new DependencyException('You must supply a valid callback.');
			}

			if (!is_bool($singleton)) {
				throw new DependencyException('The singleton parameter must be boolean.');
			}

			$this->rules[$identifier] = array(
				'callback'  => $rule,
				'singleton' => $singleton,
			);

			return $this;
		}


		/**
		 * @param string   $identifier
		 * @param callable $rule
		 *
		 * @returns self
		 * @throws DependencyException
		 */
		public function setSingletonRule ($identifier, $rule)
		{
			return $this->setRule($identifier, $rule, true);
		}


		/**
		 * @param string $identifier
		 * @param object $object
		 *
		 * @throws DependencyException
		 */
		public function storeObject ($identifier, $object)
		{
			if (!is_object($object)) {
				throw new DependencyException('You must pass an object to storeObject');
			}

			$this->rules[$identifier] = array(
				'callback'  => null,
				'singleton' => true,
			);

			$this->instances[$identifier] = $object;
		}


		/**
		 * @param string $identifier
		 * @param array  $parameters
		 *
		 * @return mixed
		 * @throws DependencyException
		 */
		public function getObject ($identifier, $parameters = array())
		{
			if (!isset($this->rules[$identifier])) {
				throw new DependencyException('Unknown object or rule ' . var_export($identifier, true) . '.');
			}

			if (!is_array($parameters)) {
				throw new DependencyException('You must supply an array of parameters.');
			}

			$rule = $this->rules[$identifier];

			if ($rule['singleton'] && !isset($this->instances[$identifier])) {
				$instance = $this->createObject(
					$rule['callback'],
					$parameters
				);

				$this->instances[$identifier] = $instance;

				return $instance;

			} else if ($rule['singleton']) {
				return $this->instances[$identifier];

			} else {
				return $this->createObject(
					$rule['callback'],
					$parameters
				);
			}
		}


		/**
		 * @param callable $rule
		 * @param array    $parameters
		 *
		 * @return mixed
		 */
		protected function createObject ($rule, $parameters = array())
		{
			return call_user_func_array(
				$rule,
				array_merge(
					array($this),
					$parameters
				)
			);
		}
	}