<?php
namespace Cena\Cena\Validation;

/**
 * Interface ValidationInterface
 *
 * @package Cena\Cena\Validation
 */
interface ValidatorInterface
{
    /**
     * set entity object. 
     * 
     * @param object $entity
     * @return mixed
     */
    public function setEntity( $entity );

    /**
     * @param array $input
     * @return mixed
     */
    public function setInput( $input );

    /**
     * validate the input.
     *
     * @return array
     */
    public function validate();

    /**
     * verify that the entity is valid. 
     * 
     * @return bool
     */
    public function verify();

    /**
     * @return bool
     */
    public function isValid();
    
    /**
     * get the errors as array.
     * returns array as 
     *     [ 'key' => 'error message' ]
     * 
     * @return array
     */
    public function getErrors();
}