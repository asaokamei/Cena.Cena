<?php
namespace Cena\Cena\EmAdapter;

use Cena\Cena\CenaManager;

class ManipulateEntity
{
    /**
     * @var EmAdapterInterface
     */
    protected $ema;

    /**
     * @var CenaManager
     */
    protected $cm;

    /**
     * @var object
     */
    protected $entity;
    
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
     * @param CenaManager $cm
     */
    public function setCenaManager( $cm )
    {
        $this->cm = $cm;
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
     */
    public function delEntity()
    {
        $this->ema->deleteEntity( $this->entity );
    }

    /**
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
     * @param array  $data
     */
    public function relate( $data )
    {
        foreach( $data as $name => $target ) {

            $this->link( $name, $target );
        }
    }

    /**
     * @param $name
     * @param $target
     * @return $this
     */
    public function link( $name, $target )
    {
        if( is_string( $target ) ) {
            $target = $this->cm->fetch( $target );
        } elseif( is_array( $target ) ) {
            foreach( $target as $key => $t ) {
                if( is_string( $t ) ) {
                    $target[$key] = $this->cm->fetch( $t );
                }
            }
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
}