<?php
namespace Cena\Cena\EmAdapter;

/**
 * Interface EmAdapterInterface
 *
 * EntityManager Adapter Interface;
 * a common API for Entity Managers, such as Doctrine2.
 *
 * @package Cena\Cena\EmAdapter
 */
interface EmAdapterInterface
{
    /**
     * @api
     * @return mixed
     */
    public function em();

    /**
     * saves entities to database.
     * @api
     */
    public function save();

    /**
     * clears the entity cache.
     * @api
     */
    public function clear();

    /**
     * @api
     * @param       $class
     * @return object
     */
    public function newEntity( $class );

    /**
     * @api
     * @param $class
     * @param $id
     * @return null|object
     */
    public function findEntity( $class, $id );

    /**
     * @api
     * @param object $entity
     * @return mixed
     */
    public function deleteEntity( $entity );

    /**
     * @api
     * get id value of the entity.
     * 
     * @param object $entity
     * @return string
     */
    public function getId( $entity );

    /**
     * get the value of the $name field.
     *
     * @param object $entity
     * @param string $key
     * @return mixed
     */
    public function getFieldValue( $entity, $key );

    /**
     * get list of fields in an entity.
     *
     * @param object $entity
     * @return array
     */
    public function getFieldList( $entity );

    /**
     * get list of fields in an entity.
     *
     * @param object $entity
     * @return array
     */
    public function getRelationList( $entity );

    /**
     * returns if the $entity object is marked as delete.
     *
     * @api
     * @param object $entity
     * @return mixed
     */
    public function isDeleted( $entity );

    /**
     * returns if the $entity object is retrieved from data base. 
     *
     * @api
     * @param $entity
     * @return mixed
     */
    public function isRetrieved( $entity );

    /**
     * returns if the $object is a collection of entities or not. 
     *
     * @param object $object
     * @return mixed
     */
    public function isCollection( $object );
    
    /**
     * populate an entity with array data.
     *
     * @param object $entity
     * @param array $data
     * @return mixed
     */
    public function assign( $entity, $data );
}