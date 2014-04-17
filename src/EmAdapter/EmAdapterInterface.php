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
     * relate the $object with $target as $name association. 
     * return true if handled in this method, or return false. 
     * 
     * @param object $object
     * @param string $name
     * @param object $target
     * @return bool
     */
    public function relate( $object, $name, $target );
}