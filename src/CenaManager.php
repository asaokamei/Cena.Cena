<?php
namespace Cena\Cena;

class CenaManager
{
    const TYPE_NEW = '0';
    const TYPE_GET = '1';
    
    /** @var string  */
    public $cena = 'Cena';
    
    /** @var int  */
    protected $new_id = 1;

    /**
     * @var \Cena\Cena\EmAdapterInterface
     */
    protected $ema;
    
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
     * convert model to class name [model => class]
     * @var array
     */
    protected $modelClass = array();
    /**
     * @param EmAdapterInterface $ema
     */
    public function __construct( $ema )
    {
        $this->ema = $ema;
    }

    /**
     * @param string $cenaId
     * @param object $entity
     */
    public function register( $cenaId, $entity )
    {
        $this->cenaEntities[ $cenaId ] = $entity;
        $this->entityCena[ spl_object_hash( $entity ) ] = $cenaId;
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
     * set model/class relation. 
     * @param string      $class
     * @param null|string $model
     */
    public function setClass( $class, $model=null )
    {
        if( !$model ) {
            $model = substr( $class, stripos( $class, '\\' )+1 );
        }
        $this->modelClass[ $model ] = $class;
    }
    
    /**
     * @param $model
     * @param $type
     * @param $id
     * @return string
     */
    public function composeCenaId( $model, $type, $id )
    {
        return "{$model}.{$type}.{$id}";
    }

    /**
     * @param $cenaId
     * @return array
     */
    public function deComposeCenaId( $cenaId )
    {
        $list = explode( '.', $cenaId );
        $list = rsort( $list );
        return array( $list[0], $list[1], $list[2] );
    }
    
    /**
     * get class name from model name.
     * 
     * @param $model
     * @return string
     */
    public function getClass( $model ) 
    {
        return isset( $this->modelClass[$model] ) ? $this->modelClass[$model]: $model;
    }

    /**
     * @param $cenaId
     * @return object
     */
    public function fetch( $cenaId )
    {
        if( $entity = $this->retrieve( $cenaId ) ) {
            return $entity;
        }
        list( $model, $type, $id ) = $this->deComposeCenaId( $cenaId );
        if( $type === self::TYPE_NEW ) {
            return $this->newEntity( $model, $id );
        }
        return $this->getEntity( $model, $id );
    }

    /**
     * @param      $model
     * @param null $id
     * @return object
     */
    public function newEntity( $model, $id=null )
    {
        if( !$id ) {
            $id = $this->new_id;
        }
        $class  = $this->getClass( $model );
        $entity = $this->ema->newEntity( $class );
        $cenaId = $this->composeCenaId( $model, self::TYPE_NEW, $id );
        $this->new_id = $id + 1;
        $this->register( $cenaId, $entity );
        return $entity;
    }

    /**
     * @param $model
     * @param $id
     * @return object
     */
    public function getEntity( $model, $id )
    {
        $class  = $this->getClass( $model );
        $entity = $this->ema->findEntity( $class, $id );
        $cenaId = $this->composeCenaId( $model, self::TYPE_GET, $id );
        $this->register( $cenaId, $entity );
        return $entity;
    }

    /**
     * @param $entity
     * @param $data
     */
    public function assign( $entity, $data )
    {
        $this->ema->loadData( $entity, $data );
    }
}
