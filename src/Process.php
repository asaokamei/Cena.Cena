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
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $model
     * @param string $prop
     * @throws \RuntimeException
     * @return $this
     */
    public function cleanNew( $model, $prop )
    {
        if( !isset( $this->source[ $this->cm->cena ] ) ) {
            throw new \RuntimeException( "cannot clean for non-Cena post input." );
        }
        foreach( $this->source[ $this->cm->cena ] as $modelName => $modelData ) {
            if( $model != $modelName ) continue;
            if( !isset( $modelData[ CenaManager::TYPE_NEW ] ) ) continue;
            foreach( $modelData[ CenaManager::TYPE_NEW ] as $id => $info ) {
                if( !isset( $info['prop'][$prop] ) || !$info['prop'][$prop] ) {
                    unset( $this->source[ $this->cm->cena ][$modelName][CenaManager::TYPE_NEW][$id] );
                }
            } 
        }
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
        $this->source = $this->prepareSource($this->source);
        if( !$data ) $data = $this->source;
        $isValid = true;
        foreach( $data as $cenaID => $info )
        {
            $isValid &= $this->cm->process( $cenaID, $info );
        }
        return $isValid;
    }
}