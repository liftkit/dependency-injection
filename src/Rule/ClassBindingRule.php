<?php


	namespace LiftKit\DependencyInjection\Rule;

	use LiftKit\DependencyInjection\Container\Container;
	use LiftKit\DependencyInjection\ClassIndex\ClassIndex;
	use LiftKit\DependencyInjection\Exception\Dependency;
	use ReflectionClass;


	class ClassBindingRule extends Rule
	{
		protected $container;
		protected $classIndex;
		protected $className;


		public function __construct (Container $container, ClassIndex $classIndex, $className)
		{
			$this->container  = $container;
			$this->classIndex = $classIndex;
			$this->className  = $className;
		}


		public function resolve (array $params = array())
		{
			$className       = $this->classIndex->resolveClassToAlias($this->className);
			$reflectionClass = new ReflectionClass($className);
			$constructor     = $reflectionClass->getConstructor();
			$finalParams     = $params;

			if ($constructor) {
				$constructorParameters = $constructor->getParameters();

				if (count($params)) {
					array_splice($constructorParameters, -count($params));
				}

				foreach ($constructorParameters as $index => $constructorParam) {
					$class = $constructorParam->getClass();

					if (! $class) {
						throw new Dependency('Only valid type-hinted classnames can be auto-resolved.');
					}

					$className = $class->getName();
					$identifier = $this->classIndex->resolveClassToRule($className);

					if (! $identifier) {
						$identifier = uniqid();
						$this->container->bindRuleToClass($identifier, $className);
					}

					array_unshift(
						$finalParams,
						$this->container->getObject($identifier)
					);
				}
			}

			return $reflectionClass->newInstanceArgs($finalParams);
		}
	}