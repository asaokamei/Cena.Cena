<?php
namespace Cena\Cena\Utils;

class ClassMap
{
    /**
     * convert model to class name [model => class]
     * @var array
     */
    protected $modelClass = array();

    /**
     * convet class name to model. 
     * 
     * @var array
     */
    protected $classModel = array();

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
        $this->classModel[ $class ] = $model;
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
     */
    public function getModel( $class )
    {
        return isset( $this->classModel[$class] ) ? $this->classModel[$class]: $class;
    }

}