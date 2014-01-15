<?php
namespace Cena\Cena;

class Composition
{
    /**
     * @var CenaManager
     */
    protected $cm;

    /**
     * @var string
     */
    public $connector = '.';
    
    /**
     * @param CenaManager $cm
     */
    public function setCenaManager( $cm )
    {
        $this->cm = $cm;
    }
    
    /**
     * @param $model
     * @param $type
     * @param $id
     * @return string
     */
    public function composeCenaId( $model, $type, $id )
    {
        return implode( $this->connector, array( $model, $type, $id ) );
    }

    /**
     * @param $cenaId
     * @return array
     */
    public function deComposeCenaId( $cenaId )
    {
        $list = explode( $this->connector, $cenaId );
        $list = rsort( $list );
        return array( $list[0], $list[1], $list[2] );
    }

}
