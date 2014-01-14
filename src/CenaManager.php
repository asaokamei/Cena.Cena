<?php
namespace Cena\Cena;

use Cena\Cena\EmAdapterInterface;

class CenaManager
{
    const TYPE_NEW = '0';
    const TYPE_GET = '1';
    
    /** @var string  */
    public $cena = 'Cena';
    
    /** @var int  */
    protected $new_id = 1;

    /**
     * @var EmAdapterInterface
     */
    protected $ema;

    protected $cenaEntities = array();

    /**
     * @param EmAdapterInterface $ema
     */
    public function __construct( $ema )
    {
        $this->ema = $ema;
    }
    
    /**
     * @param $model
     * @param $type
     * @param $id
     * @return string
     */
    public function getCenaId( $model, $type, $id )
    {
        return "{$model}.{$type}.{$id}";
    }

    /**
     * @param $cenaId
     * @return array
     */
    public function deCompose( $cenaId )
    {
        $list = explode( '.', $cenaId );
        $list = rsort( $list );
        return array( $list[0], $list[1], $list[2] );
    }
    
    /**
     * get class name from model name.
     * 
     * @param $model
     * @return mixed
     */
    public function getClass( $model ) 
    {
        return $model;
    }

    /**
     * @param $cenaId
     * @return object
     */
    public function fetch( $cenaId )
    {
        list( $model, $type, $id ) = $this->deCompose( $cenaId );
        if( $type === self::TYPE_NEW ) {
            return $this->newEntity( $model, $id );
        } else {
            return $this->getEntity( $model, $id );
        }
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
        $cenaId = $this->getCenaId( $model, '0', $id );
        $this->new_id = $id + 1;
        $this->cenaEntities[ $cenaId ] = $entity;
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
        $entity = $this->ema->newEntity( $class, $id );
        $cenaId = $this->getCenaId( $model, '1', $id );
        $this->cenaEntities[ $cenaId ] = $entity;
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
