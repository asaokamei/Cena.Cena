<?php
namespace Cena\Cena\Validation;

/**
 * Class SimpleValidatorAbstract
 * 
 * an abstract class for a simple and basic validation methods. 
 *
 * @package Cena\Cena\Validation
 */
abstract class SimpleValidatorAbstract extends DumbValidatorAbstract
{
    protected $originalInput = array(
        'prop' => [],
        'link' => [],
    );
    
    protected $messages = array(
        'encoding' => 'invalid character',
        'required' => 'required field',
    );
    
    /**
     * @param array $input
     * @return mixed
     */
    public function setInput( $input )
    {
        $this->originalInput = $input;
        $this->input = $input;
        if( isset( $this->input['prop'] ) ) {
            unset( $this->input['prop'] );
        }
    }

    /**
     * validate the input.
     *
     * @return array
     */
    public function validate()
    {
        return $this->input;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function useAsInput( $name, $value )
    {
        $this->originalInput[ 'prop' ][ $name ] = $value;
    }

    /**
     * get property value from $input['prop'].
     * sets error when encoding check fails. 
     *
     * @param string $name
     * @param string $default
     * @return mixed
     */
    protected function get( $name, $default='' )
    {
        if( !array_key_exists( $name, $this->originalInput['prop'] ) ) {
            return $default;
        }
        $value = $this->originalInput['prop'][$name];
        if( !$this->check_encoding( $value ) ) {
            $this->error( $name, $this->messages['encoding'] );
            return null;
        }
        return $value;
    }

    /**
     * use $name as a property to populate the entity. 
     * returns the input value. 
     * 
     * @param string $name
     * @param string $default
     * @return mixed
     */
    protected function property( $name, $default='' )
    {
        $value = $this->get( $name, $default );
        $this->input['prop'][$name] = $value;
        return $value;
    }

    /**
     * use $name as a required property to populate the entity. 
     * returns the input value. or returns false and sets error. 
     * 
     * @param string $name
     * @return bool|mixed
     */
    protected function required( $name )
    {
        $value = $this->property( $name );
        if( !$this->check_required( $value ) ) {
            $this->input['prop'][$name] = $value;
            $this->error( $name, $this->messages['required'] );
            return false;
        }
        return $value;
    }

    /**
     * sets error and message. 
     * 
     * @param $name
     * @param $message
     */
    protected function error( $name, $message )
    {
        $this->isValid = false;
        $this->errors[ $name ] = $message;
    }
    
    /**
     * @param $value
     * @return bool
     */
    protected function check_encoding( $value )
    {
        return mb_check_encoding( $value, $this->encode );
    }

    /**
     * @param $value
     * @return bool
     */
    protected function check_required( $value )
    {
        return "" != "{$value}";
    }
}