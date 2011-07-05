<?php

require_once 'Zend/Session/Validator/Interface.php';

require_once 'ISessionValidator.interface.php';

class ZendValidatorProxy implements Zend_Session_Validator_Interface
{
	/**
	 * @var ISessionValidator
	 */
	private $validator;
	
	public function __construct(ISessionValidator $validator)
	{
		$this->validator = $validator;
	}
	
	/**
     * Setup() - this method will store the environment variables
     * nessissary to be able to validate against in future requests.
     */
    public function setup()
    {
    	$this->validator->setup();
    }

    /**
     * Validate() - this method will be called at the beginning of
     * every session to determine if the current environment matches
     * that which was store in the setup() procedure.
     */
    public function validate()
    {
    	$this->validator->validate();
    }
}