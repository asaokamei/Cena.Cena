<?php
namespace Cena\Cena\Utils;
use Cena\Cena\CenaManager;

/**
 * Class HtmlForms
 * 
 * a helper class for managing html form elements with cena.
 *
 * @package Cena\Cena\Utils
 */
class HtmlForms implements \ArrayAccess
{
    /**
     * @var CenaManager
     */
    protected $cm;

    /**
     * @var object
     */
    protected $entity;

    /**
     * @param CenaManager $cm
     */
    public function __construct( $cm )
    {
        $this->cm = $cm;
    }

    /**
     * @param object $entity
     */
    public function setEntity( $entity )
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getCenaId()
    {
        $cenaId = $this->cm->register( $this->entity );
        return $cenaId;
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        $name = $this->cm->formBase( $this->entity );
        return $name;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get( $name )
    {
        return $this->cm->manipulate( $this->entity )->get( $name );
    }

    /**
     * @param $value
     * @return string
     */
    public function h( $value )
    {
        if( is_object( $value ) && method_exists( $value, '__toString' ) ) {
            $value = (string) $value;
        }
        if( is_string( $value ) ) {
            $value = htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
        }
        return $value;
    }

    /**
     * @return bool
     */
    public function isRetrieved()
    {
        return $this->cm->getEntityManager()->isRetrieved( $this->entity );
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->cm->manipulate( $this->entity )->isDeleted();
    }

    /**
     * @return bool
     */
    public function isError()
    {
        if( $this->cm->getCollection()->getErrors( $this->getCenaId() ) ) {
            return true;
        }
        return false;
    }

    /**
     * @param $name
     * @return null
     */
    public function getError( $name )
    {
        $errors = $this->cm->getCollection()->getErrors( $this->getCenaId() );
        return array_key_exists( $name, $errors )? $errors[$name] : null;
    }

    /**
     * @param        $name
     * @param string $class
     * @return null|string
     */
    public function getErrorMsg( $name, $class='error-msg' )
    {
        $message = $this->getError( $name );
        if( $message ) {
            $message = "<span class=\"{$class}\">{$message}</span>";
        }
        return $message;
    }

    /**
     * @param $bool
     * @return string
     */
    public function checkIf( $bool )
    {
        if( $bool ) {
            return ' checked="checked"';
        }
        return '';
    }

    /**
     * @param $bool
     * @return string
     */
    public function selectIf( $bool )
    {
        if( $bool ) {
            return ' selected="selected"';
        }
        return '';
    }

    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function isEqualTo( $name, $value )
    {
        if( $this->get( $name ) == $value ) {
            return true;
        }
        return false;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \RuntimeException
     */
    public function __call( $method, $args )
    {
        if( !method_exists( $this->entity, $method ) ) {
            throw new \RuntimeException( 'method does not exist: '.$method );
        }
        $value = call_user_func_array( array( $this->entity, $method ), $args );
        $value = $this->h( $value );
        return $value;
    }
    /**
     * Whether a offset exists
     * @param mixed $offset    An offset to check for.
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset )
    {
        return true;
    }

    /**
     * Offset to retrieve
     * @param mixed $offset  The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset )
    {
        $value = $this->get($offset);
        $value = $this->h( $value );
        return $value;
    }

    /**
     * Offset to set
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @throws \RuntimeException
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        throw new \RuntimeException( 'cannot set value in HtmlForms object' );
    }

    /**
     * Offset to unset
     * @param mixed $offset
     * @throws \RuntimeException
     * @return void
     */
    public function offsetUnset( $offset )
    {
        throw new \RuntimeException( 'cannot unset value in HtmlForms object' );
    }
}