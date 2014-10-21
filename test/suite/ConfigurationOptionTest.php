<?php
class ConfigurationOptionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test fixture
	 * @var ConfigurationFixture
	 */
	protected $fixture;
	
	public function setUp() {
		$this->fixture = new ConfigurationFixture();
		$this->fixture->setConfig(['a' => 1]);
	}
	
	public function testHasOption() {
		$this->fixture->setOption('has_option', 1);
		$this->assertTrue($this->fixture->hasOption('has_option'));
	
		$this->assertFalse($this->fixture->hasOption('not_available'));
	}
	
	public function testSetOption() {
		//boolean option
		$this->fixture->setOption('boolean_val', true);
		$this->assertEquals(true, $this->fixture->getOption('boolean_val'));
	
		//integer
		$this->fixture->setOption('int_val', 100);
		$this->assertEquals(100, $this->fixture->getOption('int_val'));
	
		//float
		$this->fixture->setOption('float_val', 2.35);
		$this->assertEquals(2.35, $this->fixture->getOption('float_val'));
	
		//string
		$this->fixture->setOption('str_val', "config");
		$this->assertEquals("config", $this->fixture->getOption('str_val'));
	}
	
	public function testGetEmpty() {
		$this->assertNull($this->fixture->getOption('b'));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetFail() {
		$this->fixture->setOption(true, true);
	}
}
?>