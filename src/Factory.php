<?php
namespace Cena\Cena;

use Cena\Cena\EmAdapter\EmAdapterInterface;
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
     * @var HtmlForms
     */
    public static  $form;

    /**
     * factory method for CenaManager.
     *
     * @param null|EmAdapterInterface $ema
     * @return \Cena\Cena\CenaManager
     */
    public static function cm( $ema=null )
    {
        self::$cm = new CenaManager(
            new Composition(),
            new Collection(),
            new ClassMap()
        );
        self::$cm->setEntityManager( $ema );
        return self::$cm;
    }

    /**
     * @param null|CenaManager $cm
     * @throws \RuntimeException
     * @return HtmlForms
     */
    public static function form( $cm=null )
    {
        if( !$cm ) $cm = self::$cm;
        self::$form = new HtmlForms( $cm );
        return self::$form;
    }
}