<?php
namespace Cena\Cena\Validation;

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
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function setInput( $input )
    {
        $this->input = $input;
    }

    /**
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
     * @return bool
     */
    public function verify()
    {
        return true;
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