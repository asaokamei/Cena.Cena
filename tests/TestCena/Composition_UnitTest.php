<?php
namespace Tests\TestCena;

use Cena\Cena\CenaManager;
use Cena\Cena\Utils\ClassMap;
use Cena\Cena\Utils\Collection;
use Cena\Cena\Utils\Composition;

class Composition_UnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Composition
     */
    var $c;

    function setUp()
    {
        include( __DIR__ . '/../autotest.php' );
        $this->c = new Composition();
        $this->cm = new CenaManager(
            $this->c,
            new Collection(),
            new ClassMap()
        );
    }

    function test0()
    {
        $this->assertEquals( 'Cena\Cena\Utils\Composition', get_class( $this->c ) );
    }

    /**
     * @test
     */
    function deComposeCenaID_splits_to_list()
    {
        $list = $this->c->deComposeCenaId( 'test.1.2' );
        $this->assertEquals( 'test', $list[0] );
        $this->assertEquals( '1', $list[1] );
        $this->assertEquals( '2', $list[2] );
    }
    
    /**
     * @test
     */
    function makeFormName_makes_some_name()
    {
        $name = $this->c->makeFormName( 'test.0.2', 'prop', 'id' );
        $this->assertEquals( 'Cena[test][0][2][prop][id]', $name );
    }
}