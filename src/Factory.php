<?php
namespace Cena\Cena;

use Cena\Cena\EmAdapter\EmAdapterInterface;
use Cena\Cena\EmAdapter\ManipulateEntity;
use Cena\Cena\Utils\ClassMap;
use Cena\Cena\Utils\Collection;
use Cena\Cena\Utils\Composition;
use Cena\Cena\Utils\HtmlForms;

class Factory
{
    /**
     * @var CenaManager
     */
    public static  $cm;

    /**
     * @var Process
     */
    public static $process;
    
    /**
     * @var HtmlForms
     */
    public static  $form;

    /**
     * factory method for CenaManager.
     *
     * @param null|EmAdapterInterface $ema
     * @return \Cena\Cena\CenaManager
     */
    public static function getCenaManager( $ema=null )
    {
        if( !self::$cm ) {
            self::$cm = self::buildCenaManager( $ema );
        }
        return self::$cm;
    }

    /**
     * @param null|EmAdapterInterface $ema
     * @return CenaManager
     */
    public static function buildCenaManager( $ema=null )
    {
        $cm = new CenaManager(
            new Composition(),
            new Collection(),
            new ClassMap(),
            new ManipulateEntity()
        );
        $cm->setEntityManager( $ema );
        return $cm;
    }

    /**
     * @return Process
     */
    public static function getProcess()
    {
        if( !self::$process ) {
            self::$process = self::buildProcess();
        }
        return self::$process;
    }

    /**
     * @param null|CenaManager $cm
     * @return Process
     */
    public static function buildProcess( $cm=null )
    {
        if( !$cm ) {
            $cm = self::getCenaManager();
        }
        $process = new Process( $cm );
        return $process;
    }

    /**
     * @param null|CenaManager $cm
     * @throws \RuntimeException
     * @return HtmlForms
     */
    public static function getHtmlForms( $cm=null )
    {
        if( !self::$form ) {
            self::$form = self::buildHtmlForms( $cm );
        }
        return self::$form;
    }

    /**
     * @param null $cm
     * @return HtmlForms
     */
    public static function buildHtmlForms( $cm=null )
    {
        if( !$cm ) $cm = self::getCenaManager();
        $form = new HtmlForms( $cm );
        return $form;
    }
}