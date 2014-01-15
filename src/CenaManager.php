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
     * @var Composition
     */
    protected $composer;

    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var \Cena\Cena\EmAdapterInterface
     */
    protected $ema;
    
    /**
     * convert model to class name [model => class]
     * @var array
     */
    protected $modelClass = array();

    /**
     * @param Composition $composer
     * @param Collection  $collection
     */
    public function __construct( $composer, $collection )
    {
        $composer->setCenaManager( $this );
        $this->composer = $composer;
        $this->collection = $collection;
    }

    /**
     * @param EmAdapterInterface $ema
     */
    public function setEntityManager( $ema )
    {
        $this->ema = $ema;
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
        if( $entity = $this->collection->retrieve( $cenaId ) ) {
            return $entity;
        }
        list( $model, $type, $id ) = $this->composer->deComposeCenaId( $cenaId );
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
        $cenaId = $this->composer->composeCenaId( $model, self::TYPE_NEW, $id );
        $this->new_id = $id + 1;
        $this->collection->register( $cenaId, $entity );
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
        $cenaId = $this->composer->composeCenaId( $model, self::TYPE_GET, $id );
        $this->collection->register( $cenaId, $entity );
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
