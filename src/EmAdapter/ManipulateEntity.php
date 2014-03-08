<?php
namespace Cena\Cena\EmAdapter;

use Cena\Cena\CenaManager;

class ManipulateEntity
{
    /**
     * @var EmAdapterInterface
     */
    protected $ema;

    /**
     * @var CenaManager
     */
    protected $cm;
    
    protected $methods = array(
        'prop' => 'assign',
        'link' => 'relate',
        'del'  => 'delEntity'
    );

    /**
     */
    public function __construct()
    {
    }

    /**
     * @param EmAdapterInterface $ema
     */
    public function setEmAdapter( $ema )
    {
        $this->ema = $ema;
    }

    /**
     * @param CenaManager $cm
     */
    public function setCenaManager( $cm )
    {
        $this->cm = $cm;
    }

    /**
     * @param object $entity
     * @param array  $info
     */
    public function process( $entity, $info )
    {
        foreach( $info as $manipulate => $data ) {
            if( !isset( $this->methods[$manipulate] ) ) continue;
            $method = $this->methods[$manipulate];
            $this->$method( $entity, $data );
        }
    }

    /**
     * @param object $entity
     */
    public function delEntity( $entity )
    {
        $this->ema->deleteEntity( $entity );
    }

    /**
     * @param $entity
     * @param $data
     */
    public function assign( $entity, $data )
    {
        $this->ema->assign( $entity, $data );
    }

    /**
     * @param object $entity
     * @param array  $data
     */
    public function relate( $entity, $data )
    {
        foreach( $data as $name => $target ) {

            if( is_string( $target ) ) {
                $target = $this->cm->fetch( $target );
            } elseif( is_array( $target ) ) {
                if( !empty( $target ) ) {
                    foreach( $target as $k => $t ) {
                        if( $t ) {
                            $target[$k] = $this->cm->fetch($t);
                        }
                    }
                }
            }
            $this->ema->relate( $entity, $name, $target );
        }
    }
}