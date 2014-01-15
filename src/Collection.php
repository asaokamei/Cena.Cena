<?php
namespace Cena\Cena;

class Collection
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
     * @param $cenaId
     * @return null|object
     */
    public function retrieve( $cenaId )
    {
        return array_key_exists( $cenaId, $this->cenaEntities ) ? $this->cenaEntities[$cenaId] : null;
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
}