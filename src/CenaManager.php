<?php
namespace Cena\Cena;

use Cena\Cena\EmAdapter\EmAdapterInterface;
use Cena\Cena\Utils\Composition;
use Cena\Cena\Utils\Collection;

class CenaManager
{
    const TYPE_NEW = '0';
    const TYPE_GET = '1';
    
    /** @var string  */
    public $cena = 'Cena';
    
    /**
     * @var Composition
     */
    protected $composer;

    /**
     * @var Utils\Collection
     */
    protected $collection;
    
    /**
     * @var EmAdapterInterface
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
     * @return EmAdapterInterface
     */
    public function getEntityManager()
    {
        return $this->ema;
    }

    /**
     * set model/class relation. 
     * @param string      $class
     * @param null|string $model
     */
    public function setClass( $class, $model=null )
    {
        if( !$model ) {
            $model = substr( $class, strrpos( $class, '\\' )+1 );
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
     * @param $class
     * @return int|string
     * @throws \RuntimeException
     */
    public function getModel( $class )
    {
        if( !in_array( $class, $this->modelClass ) ) {
            throw new \RuntimeException( "Cannot find model for class: " . $class );
        }
        foreach( $this->modelClass as $model => $className ) {
            if( $class === $className ) {
                return $model;
            }
        }
        throw new \RuntimeException( "Cannot find model for class 2: " . $class );
    }

    /**
     * @param object      $entity
     * @param null|string $cenaId
     * @return string
     */
    public function register( $entity, $cenaId=null )
    {
        if( $cenaId ) {
            $this->collection->register( $cenaId, $entity );
            return $cenaId;
        }
        if( $this->ema->isRetrieved( $entity ) ) {
            $type = self::TYPE_GET;
            $id   = $this->getId( $entity );
        } else {
            $type = self::TYPE_NEW;
            $id   = $this->composer->getNewId();
        }
        $model = $this->getModel( get_class( $entity ) );
        $cenaId = $this->composer->composeCenaId( $model, $type, $id );
        $this->collection->register( $cenaId, $entity );
        return $cenaId;
    }

    /**
     * @param $entity
     * @return string
     */
    public function getId( $entity )
    {
        $id = $this->ema->getId( $entity );
        $id = $this->composer->composeId( $id );
        return $id;
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
        $id     = $this->composer->getNewId( $id );
        $class  = $this->getClass( $model );
        $entity = $this->ema->newEntity( $class );
        $cenaId = $this->composer->composeCenaId( $model, self::TYPE_NEW, $id );
        $this->register( $entity, $cenaId );
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
        $this->register( $entity, $cenaId );
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

    /**
     * get cenaID from an entity object.
     *
     * @param $entity
     * @return null|string
     */
    public function cenaId( $entity )
    {
        return $this->collection->findCenaId( $entity );
    }

    /**
     * saves entities to database via EmAdapter.
     */
    public function save()
    {
        $this->ema->save();
    }
}
