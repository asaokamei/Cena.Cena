<?php
namespace Tests\TestCena;

use Cena\Cena\CenaManager;
use Cena\Cena\EmAdapter\ManipulateEntity;
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
            new ClassMap(),
            new ManipulateEntity()
        );
    }

    function test0()
    {
        $this->assertEquals( 'Cena\Cena\Utils\Composition', get_class( $this->c ) );
    }

    /**
     * @test
     */
    function composeCenaId_makes_the_id()
    {
        $id = $this->c->composeCenaId( 'model', 'type', 'id' );
        $this->assertEquals( 'model.type.id', $id );
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

    /**
     * @test
     */
    function getNewId_return_sequence_of_int()
    {
        $id1 = $this->c->getNewId();
        $id2 = $this->c->getNewId();
        $id3 = $this->c->getNewId();
        $this->assertEquals( '1', $id1 );
        $this->assertEquals( '2', $id2 );
        $this->assertEquals( '3', $id3 );
    }

    /**
     * @test
     */
    function getNewId_with_id_resets_to_the_id()
    {
        $id1 = $this->c->getNewId();
        $id2 = $this->c->getNewId(5);
        $id3 = $this->c->getNewId();
        $this->assertEquals( '1', $id1 );
        $this->assertEquals( '5', $id2 );
        $this->assertEquals( '6', $id3 );
    }
}