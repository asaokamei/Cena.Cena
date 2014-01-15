<?php
namespace Cena\Cena;

class Process
{
    /**
     * @var CenaManager
     */
    protected $cm;
    
    protected $source = array();
    
    public function setSource( $source )
    {
        $this->source = $source;
    }

    /**
     * process cena post data from html form.
     *
     * @return bool
     */
    public function posts()
    {
        $source = $this->source[ $this->cm->cena ];
        $data   = array();
        foreach( $source as $model => $types ) 
        {
            foreach( $types as $type => $ids ) 
            {
                foreach( $ids as $id => $info ) 
                {
                    $cenaID = $this->cm->composeCenaId( $model, $type, $id );
                    $data[ $cenaID ] = $info;
                }
            }
        }
        return $this->process( $data );
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process( $data )
    {
        $isValid = true;
        foreach( $data as $cenaID => $info )
        {
            $entity = $this->cm->fetch( $cenaID );
            $this->cm->assign( $entity, $info );
        }
        return $isValid;
    }
}