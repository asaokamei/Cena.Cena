<?php
namespace Cena\Cena;

class Process
{
    /**
     * @var CenaManager
     */
    protected $cm;
    
    protected $source = array();

    /**
     * @param CenaManager $cm
     */
    public function __construct( $cm )
    {
        $this->cm = $cm;
    }
    
    /**
     * @param array $source
     * @return $this
     */
    public function setSource( $source )
    {
        $this->source = $source;
        $this->prepareSource();
        return $this;
    }

    /**
     * auto detect source type.
     * @return $this
     */
    protected function prepareSource()
    {
        if( isset( $this->source[ $this->cm->cena ] ) ) {
            // it is a post data...
            $this->setPosts( $this->source[ $this->cm->cena ] );
        }
        return $this;
    }

    /**
     * set cena post data from html form.
     *
     * @param array $source
     * @return $this
     */
    public function setPosts( $source )
    {
        foreach( $source as $model => $types ) 
        {
            foreach( $types as $type => $ids ) 
            {
                foreach( $ids as $id => $info ) 
                {
                    $cenaID = $this->cm->getComposer()->composeCenaId( $model, $type, $id );
                    $this->source[ $cenaID ] = $info;
                }
            }
        }
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process( $data=array() )
    {
        if( !$data ) $data = $this->source;
        $isValid = true;
        foreach( $data as $cenaID => $info )
        {
            $entity = $this->cm->fetch( $cenaID );
            if( isset( $info['prop'] ) ) {
                $this->cm->assign( $entity, $info['prop'] );
            }
        }
        return $isValid;
    }
}