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
     * @param object $entity
     * @return bool
     */
    public function exists( $entity )
    {
        return array_key_exists( spl_object_hash( $entity ), $this->entityCena );
    }
}