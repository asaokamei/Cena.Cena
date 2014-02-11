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
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * @param array $source
     * @return $this
     */
    public function setSource( $source )
    {
        $this->source = $this->prepareSource($source);
        return $this;
    }

    /**
     * auto detect source type.
     *
     * @param array $source
     * @return array
     */
    protected function prepareSource( $source )
    {
        if( isset( $source[ $this->cm->cena ] ) ) {
            // it is a post data...
            return $this->convertPosts( $source[ $this->cm->cena ] );
        }
        return $source;
    }

    /**
     * set cena post data from html form.
     *
     * @param array $source
     * @return array
     */
    protected function convertPosts( $source )
    {
        $input = array();
        foreach( $source as $model => $types ) 
        {
            foreach( $types as $type => $ids ) 
            {
                foreach( $ids as $id => $info ) 
                {
                    $cenaID = $this->cm->getComposer()->composeCenaId( $model, $type, $id );
                    $input[ $cenaID ] = $info;
                }
            }
        }
        return $input;
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
            if( isset( $info['link'] ) ) {
                $this->cm->relate( $entity, $info['link'] );
            }
        }
        return $isValid;
    }
}