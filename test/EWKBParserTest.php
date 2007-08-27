<?php
require_once 'PHPUnit/Extensions/ExceptionTestCase.php';
require_once dirname(__FILE__).'/../lib/GeoPHP.php';

class EWKBParserTest extends PHPUnit_Extensions_ExceptionTestCase
{
	protected $parser;
	
	protected function setUp()
	{
		$this->parser = new HexEWKBParser;
	}
	
	public function test_point2()
	{
		$point = $this->parser->parse('0101000020BC01000000000000000008400000000000001440');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(false, $point->with_z);
		$this->assertEquals(false, $point->with_m);
	}
	
	public function test_point3z()
	{
		$point = $this->parser->parse('01010000A0BC01000000000000000008400000000000001440A245B6F3FD541DC0');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->z);
	}
	
	public function test_point3m()
	{
		$point = $this->parser->parse('0101000060BC01000000000000000008400000000000001440A245B6F3FD541DC0');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->m);
	}
	
	public function test_point4()
	{
		$point = $this->parser->parse('01010000E0BC01000000000000000008400000000000001440A245B6F3FD541DC0C93EC8B2606B5A40');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->z);
		$this->assertEquals(105.677777, $point->m);
	}
	
	public function test_point_no_srid()
	{
		$point = $this->parser->parse('010100000000000000000008400000000000001440');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(GeoPHP::DEFAULT_SRID, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
	}
	
	public function test_fail_truncated_data()
	{
		$this->setExpectedException('EWKBFormatError');
		$point = $this->parser->parse('0101000020BC01000000000000000008');
	}
	
	public function test_fail_extra_data()
	{
		$this->setExpectedException('EWKBFormatError');
		$point = $this->parser->parse('0101000020BC01000000000000000008400000000000001440F00');
	}
	
	public function test_fail_bad_geometry_type()
	{
		$this->setExpectedException('EWKBFormatError');
		$point = $this->parser->parse('0109000020BC01000000000000000008400000000000001440');
	}
	
	public function test_fail_no_m()
	{
		$this->setExpectedException('EWKBFormatError');
		$point = $this->parser->parse('0101000060BC01000000000000000008400000000000001440');
	}
	
	
	/*
	public function test_point_bigendian()
	{
		// From GeoRuby test suite
		$point = $this->parser->parse('00000000014013A035BD512EC7404A3060C38F3669');
		$this->assertTrue($point instanceof GeoPHP_Point);
		$this->assertEquals(4.906455, $point->x);
		$this->assertEquals(52.377953, $point->y);
	}
	*/
}
?>
