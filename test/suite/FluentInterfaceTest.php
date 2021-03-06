<?php
class FluentInterfaceTest extends \PHPUnit_Framework_TestCase {
	public function testMerge() {
		$conf = new ConfigurationFixture();
		$conf->setConfig(['x' => 1, 'y' => 2, 'z' => 3]);

		$merged = $conf->merge(['z' => 4, 'i' => 5]);

		$this->assertTrue($merged->hasOption('x'));
		$this->assertEquals(1, $merged->getOption('x'));

		$this->assertTrue($merged->hasOption('y'));
		$this->assertEquals(2, $merged->getOption('y'));

		$this->assertTrue($merged->hasOption('z'));
		$this->assertEquals(4, $merged->getOption('z'));

		$this->assertTrue($merged->hasOption('i'));
		$this->assertEquals(5, $merged->getOption('i'));

		//invert merge order
		$merged = $conf->merge(['z' => 4, 'i' => 5], true);

		$this->assertTrue($merged->hasOption('x'));
		$this->assertEquals(1, $merged->getOption('x'));

		$this->assertTrue($merged->hasOption('y'));
		$this->assertEquals(2, $merged->getOption('y'));

		$this->assertTrue($merged->hasOption('z'));
		$this->assertEquals(3, $merged->getOption('z'));

		$this->assertTrue($merged->hasOption('i'));
		$this->assertEquals(5, $merged->getOption('i'));
	}

	public function testDiscard() {
		$conf = new ConfigurationFixture();
		$conf->setConfig(['x' => 1, 'y' => 2, 'z' => 3]);

		$discarded = $conf->discard('x');
		$this->assertFalse($discarded->hasOption('x'));
		$this->assertTrue($discarded->hasOption('y'));
		$this->assertTrue($discarded->hasOption('z'));

		$discarded = $conf->discard('x', 'y');
		$this->assertFalse($discarded->hasOption('x'));
		$this->assertFalse($discarded->hasOption('y'));
		$this->assertTrue($discarded->hasOption('z'));
	}

	public function testOption() {
		$conf = new ConfigurationFixture();
		$conf->setConfig(['x' => 1]);

		$added = $conf->option('y', 2);
		$this->assertTrue($added->hasOption('x'));
		$this->assertTrue($added->hasOption('y'));
		$this->assertEquals(2, $added->getOption('y'));

		//override option
		$added = $conf->option('x', 2);
		$this->assertTrue($added->hasOption('x'));
		$this->assertEquals(2, $added->getOption('x'));
	}

	public function testPushPop() {
		$conf = new ConfigurationFixture();
		$pushed = $conf->push('queue', 'first_val');
		$this->assertTrue($pushed->hasOption('queue'));
		$this->assertInternalType('array', $pushed->getOption('queue'));
		$this->assertCount(1, $pushed->getOption('queue'));
		$this->assertContains('first_val', $pushed->getOption('queue'));
		$pushed = $pushed->push('queue', 'second_val');
		$this->assertCount(2, $pushed->getOption('queue'));
		$this->assertContains('first_val', $pushed->getOption('queue'));
		$this->assertContains('second_val', $pushed->getOption('queue'));

		$conf = new ConfigurationFixture();
		$conf->preserveInstance = true;
		$conf->push('queue', 'first_val');
		$conf->push('queue', 'second_val');
		$pop = $conf->pop('queue');
		$this->assertEquals('second_val', $pop);
		$pop = $conf->pop('queue');
		$this->assertEquals('first_val', $pop);
		$pop = $conf->pop('queue');
		$this->assertNull($pop);

		$conf = new ConfigurationFixture();
		$extra = $conf->option('test', 'value');
		$pop = $extra->pop('test');
		$this->assertEquals('value', $pop);
		$pop = $extra->pop('test');
		$this->assertNull($pop);
		
		$conf = new ConfigurationFixture();
		$pop = $conf->pop('empty');
		$this->assertNull($pop);
		
		$conf = new ConfigurationFixture();
		$conf->setConfig(['queue' => 'first_val']);
		$pushed = $conf->push('queue', 'second_val', 'third_val');
		$this->assertCount(3, $pushed->getOption('queue'));
		$this->assertContains('first_val', $pushed->getOption('queue'));
		$this->assertContains('second_val', $pushed->getOption('queue'));
		$this->assertContains('third_val', $pushed->getOption('queue'));
	}
	
	public function testPreserveInstance() {
		$conf = new ConfigurationFixture();
		$conf->preserveInstance = true;
		$conf->setOption('test1', 'val1');
		$newConf = $conf->option('non-transient', 'val2');
		
		$this->assertTrue($newConf->hasOption('test1'));
		$this->assertTrue($newConf->hasOption('non-transient'));
		$this->assertEquals('val1', $newConf->getOption('test1'));
		$this->assertEquals('val2', $newConf->getOption('non-transient'));
		
		$this->assertTrue($conf->hasOption('test1'));
		$this->assertTrue($conf->hasOption('non-transient'));
		$this->assertEquals('val1', $conf->getOption('test1'));
		$this->assertEquals('val2', $conf->getOption('non-transient'));
		
		$this->assertEquals($newConf, $conf);
		
		$conf = new ConfigurationFixture();
		$conf->preserveInstance = true;
		$conf->merge(['test3' => 'val3', 'test4' => 'val4']);
		$this->assertTrue($conf->hasOption('test3'));
		$this->assertTrue($conf->hasOption('test4'));
		$this->assertEquals('val3', $conf->getOption('test3'));
		$conf->discard('test4');
		$this->assertFalse($conf->hasOption('test4'));
		$conf->push('values', 1, 2, 3);
		$this->assertEquals([1, 2, 3], $conf->getOption('values'));
	}
}
?>
