<?php
require_once 'C:\xampp\htdocs\reservas\api\index.php';
/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-26 at 22:39:30.
 */
class utilsReservaTest extends PHPUnit_Framework_TestCase {

    /**
     * @var utilsReserva
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new utilsReserva;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * Generated from @assert ("ab-cd", "-") == "ab".
     *
     * @covers utilsReserva::stringSeparator
     */
    public function testStringSeparator() {
        $this->assertEquals(
                "ab", $this->object->stringSeparator("ab-cd", "-")
        );
    }

    /**
     * Generated from @assert ("abc/def", "/") == "abc".
     *
     * @covers utilsReserva::stringSeparator
     */
    public function testStringSeparator2() {
        $this->assertEquals(
                "abc", $this->object->stringSeparator("abc/def", "/")
        );
    }

    /**
     * Generated from @assert ("ted<plus", "<") == "ted".
     *
     * @covers utilsReserva::stringSeparator
     */
    public function testStringSeparator3() {
        $this->assertEquals(
                "ted", $this->object->stringSeparator("ted<plus", "<")
        );
    }

    /**
     * Generated from @assert ("192.185.12.105", "solwebco_reserva","TPsKz!)IG*Fo") == true.
     *
     * @covers utilsReserva::conexionValida
     */
    public function testConexionValida() {
        $this->assertTrue(
                $this->object->conexionValida("192.185.12.105", "solwebco_reserva", "TPsKz!)IG*Fo")
        );
    }

}
