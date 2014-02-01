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
     * @var string
     */
    public $splitId = '+';

    /**
     * @var int
     */
    protected $new_id = 1;
    
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

    /**
     * @param string|array $id
     * @return string
     */
    public function composeId( $id )
    {
        if( is_string( $id ) ) {
            return $id;
        }
        return implode( $this->splitId, $id );
    }

    /**
     * @param null $id
     * @return int|null
     */
    public function getNewId( $id=null )
    {
        if( !$id ) {
            $id = $this->new_id;
        }
        $this->new_id = $id + 1;
        return $id;
    }
}
