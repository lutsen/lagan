<?php

/*
 * From https://github.com/slimphp/Slim-Csrf
 *
 * We use getTokenName() and getTokenValue() directly on the Guard middleware instance.
 *
 * With this extension, you can access the token pair in any template:
 * <input type="hidden" name="{{csrf.keys.name}}" value="{{csrf.name}}">
 * <input type="hidden" name="{{csrf.keys.value}}" value="{{csrf.value}}">
 */

class CsrfExtension extends \Twig_Extension implements Twig_Extension_GlobalsInterface {

	/**
	 * @var \Slim\Csrf\Guard
	 */
	protected $csrf;
	
	public function __construct(\Slim\Csrf\Guard $csrf)
	{
		$this->csrf = $csrf;
	}

	public function getGlobals()
	{
		// CSRF token name and value
		$csrfNameKey = $this->csrf->getTokenNameKey();
		$csrfValueKey = $this->csrf->getTokenValueKey();
		$csrfName = $this->csrf->getTokenName();
		$csrfValue = $this->csrf->getTokenValue();
		
		return [
			'csrf'   => [
				'keys' => [
					'name'  => $csrfNameKey,
					'value' => $csrfValueKey
				],
				'name'  => $csrfName,
				'value' => $csrfValue
			]
		];
	}

	public function getName()
	{
		return 'slim/csrf';
	}
}

?>