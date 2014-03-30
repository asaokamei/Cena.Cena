<?php
namespace Cena\Cena\Validation;

/**
 * Class DumbValidatorAbstract
 * 
 * a dumb validation class that does NOT validate anything. 
 * BUT it is important to use this as a starting class to 
 * validate input for Cena. 
 *
 * @package Cena\Cena\Validation
 */
abstract class DumbValidatorAbstract implements ValidatorInterface
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * @var array
     */
    protected $input;

    /**
     * @var bool
     */
    protected $isValid = true;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var string
     */
    protected $encode = 'UTF-8';

    /**
     * set entity object.
     *
     * @param object $entity
     * @return mixed
     */
    public function setEntity( $entity )
    {
        $this->entity = $entity;
        $this->isValid = true;
        $this->errors  = array();
    }

    /**
     * set the input data from post or un-trusted source.
     * example: $input = [ 'prop' => [], 'link' => [] ]
     * 
     * @param array $input
     * @return mixed
     */
    public function setInput( $input )
    {
        $this->input = $input;
    }

    /**
     * get the validated and filtered input data. 
     * 
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * validate the input.
     *
     * @return array
     */
    public function validate()
    {
    }

    /**
     * verify that the entity is valid.
     *
     * @return void
     */
    public function verify()
    {
        return ;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * get the errors as array.
     * returns array as
     *     [ 'key' => 'error message' ]
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}