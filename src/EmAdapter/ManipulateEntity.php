<?php
namespace Cena\Cena\EmAdapter;

/**
 * Class ManipulateEntity
 * 
 * a generic class to manipulate an entity using EntityManager(Adapter). 
 *
 * @package Cena\Cena\EmAdapter
 */
class ManipulateEntity
{
    /**
     * @var EmAdapterInterface
     */
    protected $ema;

    /**
     * @var object
     */
    protected $entity;

    /**
     * maps input's action to manipulation method name 
     * that are converted during *process* method. 
     * example: $methods = [ 'action' => 'method',  ]
     * 
     * @var array
     */
    protected $methods = array(
        'prop' => 'assign',
        'link' => 'relate',
        'del'  => 'delEntity'
    );

    /**
     */
    public function __construct()
    {
    }

    /**
     * @param EmAdapterInterface $ema
     */
    public function setEmAdapter( $ema )
    {
        $this->ema = $ema;
    }

    /**
     * @param object $entity
     * @return $this
     */
    public function setEntity( $entity )
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * process the input for the entity. 
     * 
     * @param array  $info
     */
    public function process( $info )
    {
        foreach( $info as $manipulate => $data ) {
            if( !isset( $this->methods[$manipulate] ) ) continue;
            $method = $this->methods[$manipulate];
            $this->$method( $data );
        }
    }

    /**
     * mark the entity as delete. 
     */
    public function delEntity()
    {
        $this->ema->deleteEntity( $this->entity );
    }

    /**
     * get a property value of $key from the entity. 
     * 
     * @param string $key
     * @return mixed
     */
    public function get( $key )
    {
        $method = 'get' . $this->makeBasicAccessor( $key );
        if( method_exists( $this->entity, $method ) ) {
            return $this->entity->$method();
        }
        if( $this->entity instanceof \ArrayAccess && array_key_exists( $key, $this->entity ) ) {
            return $this->entity[ $key ];
        }
        if( property_exists( $this->entity, $key ) ) {
            return $this->entity->$key;
        }
        // throw new \RuntimeException( "cannot set '{$key}' property of an entity" );
        return null;
    }

    /**
     * mass assign property values of the entity.
     * 
     * @param $data
     */
    public function assign( $data )
    {
        foreach( $data as $key => $value )
        {
            $this->set( $key, $value );
        }
    }

    /**
     * sets property $key as $value of the entity. 
     * 
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set( $key, $value )
    {
        $method = 'set' . $this->makeBasicAccessor( $key );
        if( method_exists( $this->entity, $method ) ) {
            $this->entity->$method( $value );
            return $this;
        }
        if( $this->entity instanceof \ArrayAccess ) {
            $this->entity[ $key ] = $value;
            return $this;
        }
        if( property_exists( $this->entity, $key ) ) {
            $this->entity->$key = $value;
            return $this;
        }
        // throw new \RuntimeException( "cannot set '{$key}' property of an entity" );
        return $this;
    }

    /**
     * mass assign links for the entity.
     * 
     * @param array  $data
     */
    public function relate( $data )
    {
        foreach( $data as $name => $target ) {

            $this->link( $name, $target );
        }
    }

    /**
     * set $name link (association, relation) from the entity to the $target entity. 
     * 
     * @param $name
     * @param $target
     * @return $this
     */
    public function link( $name, $target )
    {
        if( $this->ema->relate( $this->entity, $name, $target ) ) {
            // handled by the Ema's relation method. 
            return $this;
        }
        $method = 'set' . $this->makeBasicAccessor( $name );
        $this->entity->$method( $target );
        return $this;
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeBasicAccessor( $name )
    {
        $name = ucwords( $name );
        if( strpos( $name, '_' ) !== false ) {
            $list = explode( '_', $name );
            array_walk( $list, function(&$a){$a=ucwords($a);} );
            $name = implode( '', $list );
        }
        return $name;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->ema->isDeleted( $this->entity );
    }

    /**
     * @return mixed
     */
    public function isRetrieved()
    {
        return $this->ema->isRetrieved( $this->entity );
    }
}