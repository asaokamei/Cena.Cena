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
     * @param array  $info
     */
    public function process( $entity, $info )
    {
        foreach( $info as $manipulate => $data ) {
            if( !isset( $this->methods[$manipulate] ) ) continue;
            $method = $this->methods[$manipulate];
            $this->$method( $entity, $data );
        }
    }

    /**
     * @param object $entity
     */
    public function delEntity( $entity )
    {
        $this->ema->deleteEntity( $entity );
    }

    /**
     * @param object $entity
     * @param string $key
     * @return mixed
     */
    public function get( $entity, $key )
    {
        $method = 'get' . $this->makeBasicAccessor( $key );
        if( method_exists( $entity, $method ) ) {
            return $entity->$method();
        }
        if( $entity instanceof \ArrayAccess && array_key_exists( $key, $entity ) ) {
            return $entity[ $key ];
        }
        if( property_exists( $entity, $key ) ) {
            return $entity->$key;
        }
        // throw new \RuntimeException( "cannot set '{$key}' property of an entity" );
        return null;
    }

    /**
     * @param $entity
     * @param $data
     */
    public function assign( $entity, $data )
    {
        foreach( $data as $key => $value )
        {
            $this->set( $entity, $key, $value );
        }
    }

    /**
     * @param object $entity
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set( $entity, $key, $value )
    {
        $method = 'set' . $this->makeBasicAccessor( $key );
        if( method_exists( $entity, $method ) ) {
            $entity->$method( $value );
            return $this;
        }
        if( $entity instanceof \ArrayAccess ) {
            $entity[ $key ] = $value;
            return $this;
        }
        if( property_exists( $entity, $key ) ) {
            $entity->$key = $value;
            return $this;
        }
        // throw new \RuntimeException( "cannot set '{$key}' property of an entity" );
        return $this;
    }

    /**
     * @param object $entity
     * @param array  $data
     */
    public function relate( $entity, $data )
    {
        foreach( $data as $name => $target ) {

            $this->link( $entity, $name, $target );
        }
    }

    /**
     * @param $entity
     * @param $name
     * @param $target
     */
    public function link( $entity, $name, $target )
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
        $entity->$method( $target );
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