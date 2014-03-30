<?php
namespace Cena\Cena;

use Cena\Cena\EmAdapter\EmAdapterInterface;
use Cena\Cena\EmAdapter\ManipulateEntity;
use Cena\Cena\Utils\ClassMap;
use Cena\Cena\Utils\Composition;
use Cena\Cena\Utils\Collection;
use Cena\Cena\Validation\ValidatorInterface;

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
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var EmAdapterInterface
     */
    protected $ema;
    
    /**
     * @var ClassMap
     */
    protected $classMap;

    /**
     * @var ManipulateEntity
     */
    protected $manipulate;

    /**
     * @param Composition $composer
     * @param Collection  $collection
     * @param ClassMap    $classMap
     * @param ManipulateEntity $manipulate
     */
    public function __construct( $composer, $collection, $classMap, $manipulate )
    {
        $composer->setCenaManager( $this );
        $this->composer   = $composer;
        $this->collection = $collection;
        $this->classMap   = $classMap;
        $this->manipulate = $manipulate;
    }

    /**
     * @param EmAdapterInterface $ema
     */
    public function setEntityManager( $ema )
    {
        $this->ema = $ema;
        $this->manipulate->setEmAdapter( $ema );
    }

    /**
     * clears the EntityManager
     */
    public function clear()
    {
        $this->collection->clear();
        $this->ema->clear();
    }

    /**
     * @return EmAdapterInterface
     */
    public function getEntityManager()
    {
        return $this->ema;
    }

    /**
     * @return Composition
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param $entity
     * @return ManipulateEntity
     */
    public function manipulate( $entity )
    {
        $this->manipulate->setEntity( $entity );
        return $this->manipulate;
    }

    /**
     * set model/class relation. 
     * @param string      $class
     * @param null|string $model
     */
    public function setClass( $class, $model=null )
    {
        $this->classMap->setClass( $class, $model );
    }

    /**
     * @param string             $class
     * @param ValidatorInterface $validator
     */
    public function setValidator( $class, $validator )
    {
        $this->classMap->setValidator( $class, $validator );
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
        if( $cenaId = $this->collection->findCenaId( $entity ) ) {
            return $cenaId;
        }
        if( $this->ema->isRetrieved( $entity ) ) {
            $type = self::TYPE_GET;
            $id   = $this->getId( $entity );
        } else {
            $type = self::TYPE_NEW;
            $id   = $this->composer->getNewId();
        }
        $model = $this->classMap->getModel( get_class( $entity ) );
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
        $class  = $this->classMap->getClass( $model );
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
        $class  = $this->classMap->getClass( $model );
        $entity = $this->ema->findEntity( $class, $id );
        $cenaId = $this->composer->composeCenaId( $model, self::TYPE_GET, $id );
        $this->register( $entity, $cenaId );
        return $entity;
    }

    /**
     * @param object|string $entity
     * @param array         $info
     * @return bool
     */
    public function process( $entity, $info )
    {
        if( !is_object( $entity ) ) {
            $entity = $this->fetch( $entity );
        }
        if( isset( $info['link'] ) && !empty( $info['link'])) {
            $cm = $this;
            array_walk_recursive( $info['link'], function(&$v) use($cm) {
                if( is_string( $v ) ) {
                    $v = $cm->fetch( $v );
                }
            } );
        }
        if( !$validator = $this->classMap->getValidator( $entity ) ) {
            // no validation. process the input. 
            $this->manipulate($entity)->process( $info );
            return true;
        }
        // validate the input value, and process it, anyway. 
        $validator->setEntity( $entity );
        $validator->setInput( $info );
        $validator->validate();
        $info = $validator->getInput();
        $this->manipulate( $entity )->process( $info );
        if( !$validator->isValid() ) {
            // the input is invalid. set error and return false. 
            $this->collection->setErrors( $entity, $validator->getErrors() );
            return false;
        }
        return true; // good!
    }

    /**
     * verify that all the entities are valid. 
     * 
     * @return bool
     */
    public function verify()
    {
        $isValid = true;
        foreach( $this->collection as $entity )
        {
            if( $validator = $this->classMap->getValidator( $entity ) ) {
                $validator->setEntity( $entity );
                $validator->verify();
                if( !$validator->isValid() ) {
                    // the input is invalid. set error and return false.
                    $isValid = false;
                    $this->collection->setErrors( $entity, $validator->getErrors() );
                }
            }
        }
        return $isValid;
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

    /**
     * makes name for html form elements
     * 
     * @param object $entity
     * @param string $name
     * @param string $type
     * @return string
     */
    public function formName( $entity, $name, $type='prop' )
    {
        $cenaId = $this->register( $entity );
        $name   = $this->composer->makeFormName( $cenaId, $type, $name );
        return $name;
    }

    /**
     * @param object $entity
     * @return string
     */
    public function formBase( $entity )
    {
        $cenaId = $this->register( $entity );
        $list = $this->composer->deComposeCenaId( $cenaId );
        $form = $this->cena . '[' . implode( '][', $list ) . ']';
        return $form;
    }
}
