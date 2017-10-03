<?php

namespace Charcoal\Tests\Model;

// From 'charcoal-core'
use Charcoal\Model\AbstractMetadata;

/**
 *
 */
class AbstractMetadataTest extends \PHPUnit_Framework_TestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = $this->getMockForAbstractClass(AbstractMetadata::class);
    }

    public function testSetData()
    {
        $obj = $this->obj;
        $ret = $obj->merge([
            'properties'=>[],
            'foo'=>'bar'
        ]);
        $this->assertSame($ret, $obj);
        $this->assertEquals([], $obj->properties());
        $this->assertEquals('bar', $obj->foo);
    }

    public function testArrayAccessOffsetExists()
    {
        $obj = $this->obj;
        $this->assertFalse(isset($obj['x']));
    }
}
