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
			$resolvedParams  = array();

			if ($constructor) {
				$constructorParameters = $constructor->getParameters();

				if (count($params)) {
					array_splice($constructorParameters, -count($params));
				}

				foreach ($constructorParameters as $index => $constructorParam) {
					$class = $constructorParam->getType();

					if (! $class) {
						if ($constructorParam->isOptional()) {
							break;
						}

						throw new Dependency('Only valid type-hinted classnames can be auto-resolved.');
					}

					$className  = $class->getName();
					$identifier = $this->classIndex->resolveClassToRule($className);

					if (! $identifier) {
						$identifier = uniqid();
						$this->container->bindRuleToClass($identifier, $className);
					}

					$resolvedParams[] = $this->container->getObject($identifier);
				}
			}

			$finalParams = array_merge($resolvedParams, $params);

			return $reflectionClass->newInstanceArgs($finalParams);
		}
	}
