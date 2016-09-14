<?php

	namespace LiftKit\DependencyInjection\Rule;



	abstract class Rule
	{


		abstract public function resolve (array $params = array());
	}