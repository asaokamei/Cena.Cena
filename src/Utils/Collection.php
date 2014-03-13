<?php
namespace Cena\Cena\Utils;

use Traversable;

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * get object from cenaId [ cena-id => object ]
     * @var object[]
     */
    protected $cenaEntities = array();

    /**
     * get cenaId from object hash [ object-hash => cena-id ]
     * @var string[]
     */
    protected $entityCena = array();

    /**
     * @var array
     */
    protected $errors;

    /**
     * @param string $cenaId
     * @param object $entity
     */
    public function register( $cenaId, $entity )
    {
        if( $this->exists( $entity ) ) return;
        $this->cenaEntities[ $cenaId ] = $entity;
        $this->entityCena[ spl_object_hash( $entity ) ] = $cenaId;
    }

    /**
     * 
     */
    public function clear()
    {
        $this->cenaEntities = array();
        $this->entityCena   = array();
    }
    
    /**
     * @param $cenaId
     * @return null|object
     */
    public function retrieve( $cenaId )
    {
        return array_key_exists( $cenaId, $this->cenaEntities ) ? $this->cenaEntities[$cenaId] : null;
    }

    /**
     * @param string $model
     * @return array
     */
    public function findByModel( $model )
    {
        $found = array();
        foreach( $this->entityCena as $cenaID ) {
            list( $m, $type, $id ) = explode( '.', $cenaID );
            if( $model == $m ) {
                $found[] = $this->cenaEntities[$cenaID];
            }
        }
        return $found;
    }

    /**
     * @param $entity
     * @return null|string
     */
    public function findCenaId( $entity )
    {
        if( array_key_exists( spl_object_hash( $entity ), $this->entityCena ) ) {
            return $this->entityCena[ spl_object_hash( $entity ) ];
        }
        return null;
    }

    /**
     * @param object|string $entity
     * @throws \RuntimeException
     * @return bool
     */
    public function exists( $entity )
    {
        if( is_string( $entity ) ) {
            return array_key_exists( $entity, $this->cenaEntities );
        } elseif( is_object( $entity ) ) {
            return array_key_exists( spl_object_hash( $entity ), $this->entityCena );
        }
        throw new \RuntimeException( 'parameter must be a cenaID or an entity object. ' );
    }

    /**
     * @param object|string $entity
     */
    public function remove( $entity )
    {
        if( !$this->exists( $entity ) ) return;
        if( is_string( $entity ) ) {
            $cenaId = $entity;
            $entity = $this->cenaEntities[ $cenaId ];
            $objId  = spl_object_hash( $entity );
        } else {
            $objId  = spl_object_hash( $entity );
            $cenaId = $this->entityCena[ $objId ];
        }
        unset( $this->cenaEntities[ $cenaId ] );
        unset( $this->entityCena[ $objId ] );
    }

    /**
     * @param string|object $cenaId
     * @param mixed $errors
     */
    public function setErrors( $cenaId, $errors )
    {
        if( is_object( $cenaId ) ) {
            $cenaId = $this->findCenaId( $cenaId );
        }
        $this->errors[ $cenaId ] = $errors;
    }

    /**
     * @param string|object $cenaId
     * @return array|mixed
     */
    public function getErrors( $cenaId )
    {
        if( is_object( $cenaId ) ) {
            $cenaId = $this->findCenaId( $cenaId );
        }
        return isset( $this->errors[ $cenaId ] ) ? $this->errors[ $cenaId ]: array();
    }
    // +----------------------------------------------------------------------+
    //  for ArrayAccess and Iterator. 
    // +----------------------------------------------------------------------+
    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->cenaEntities );
    }

    /**
     * Whether a offset exists
     * @param mixed $offset  An offset to check for.
     * @return boolean       true on success or false on failure.
     */
    public function offsetExists( $offset )
    {
        return $this->exists( $offset );
    }

    /**
     * Offset to retrieve
     * @param mixed $offset  The offset to retrieve.
     * @return mixed         Can return all value types.
     */
    public function offsetGet( $offset )
    {
        return $this->retrieve( $offset );
    }

    /**
     * Offset to set
     *
     * @param mixed $offset  The offset to assign the value to.
     * @param mixed $value   The value to set.
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        $this->register( $offset, $value );
    }

    /**
     * Offset to unset
     * @param mixed $offset  The offset to unset.
     * @return void
     */
    public function offsetUnset( $offset )
    {
        $this->remove( $offset );
    }

    /**
     * Count elements of an object
     * @return int   The custom count as an integer.
     */
    public function count()
    {
        return count( $this->cenaEntities );
    }
    // +----------------------------------------------------------------------+
}