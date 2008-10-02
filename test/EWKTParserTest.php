<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../lib/GeoPHP.php';

class EWKTParserTest extends PHPUnit_Framework_TestCase
{
	protected $parser;
	
	protected function setUp()
	{
		$this->parser = new GeoPHP_EWKTParser;
	}
	
	public function test_tokenizer()
	{
		$t = new GeoPHP_EWKTTokenizer("MULTIPOINT(5.5  6, 0 -7.88 ) ");
		
		$this->assertEquals('MULTIPOINT', $t->check_next_token());
		$this->assertEquals('MULTIPOINT', $t->get_next_token());
		$this->assertEquals('(', $t->get_next_token());
		$this->assertEquals('5.5', $t->check_next_token());
		$this->assertEquals('5.5', $t->get_next_token());
		$this->assertEquals('6', $t->get_next_token());
		$this->assertEquals(',', $t->get_next_token());
		$this->assertEquals('0', $t->get_next_token());
		$this->assertEquals('-7.88', $t->get_next_token());
		$this->assertEquals(')', $t->get_next_token());
		$this->assertEquals(null, $t->get_next_token());
		$this->assertEquals(null, $t->check_next_token());
		$t->done();
	}
	
	public function test_fail_truncated_data()
	{
		$this->setExpectedException('GeoPHP_EWKTFormatError');
		$point = $this->parser->parse('POINT(4.5');
	}
	
	public function test_fail_extra_data()
	{
		$this->setExpectedException('GeoPHP_EWKTFormatError');
		// Added asdf to the end
		$point = $this->parser->parse('POINT(3.4 4)asdf');
	}
	
	public function test_fail_bad_geometry_type()
	{
		$this->setExpectedException('GeoPHP_EWKTFormatError');
		$point = $this->parser->parse('BOGUS(3 4 5)');
	}
	
	public function test_fail_no_m()
	{
		$this->setExpectedException('GeoPHP_EWKTFormatError');
		// Turned on with_m flag, but no m coordinate
		$point = $this->parser->parse('POINTM(3 4)');
	}

	public function test_point2()
	{
		$point = $this->parser->parse('SRID=444;POINT(3 5)');
		$this->assertEquals('GeoPHP_Point', get_class($point));
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(false, $point->with_z);
		$this->assertEquals(false, $point->with_m);
	}
	
	public function test_point3z()
	{
		$point = $this->parser->parse('SRID=444;POINT(3 5 -7.333)');
		$this->assertEquals('GeoPHP_Point', get_class($point));
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->z);
	}
	
	public function test_point3m()
	{
		$point = $this->parser->parse('SRID=444;POINTM(3 5 -7.333)');
		$this->assertEquals('GeoPHP_Point', get_class($point));
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->m);
	}
	
	public function test_point4()
	{
		$point = $this->parser->parse('SRID=444;POINT(3 5 -7.333 105.677777)');
		$this->assertEquals('GeoPHP_Point', get_class($point));
		$this->assertEquals(444, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
		$this->assertEquals(-7.333, $point->z);
		$this->assertEquals(105.677777, $point->m);
	}
	
	public function test_point_no_srid()
	{
		$point = $this->parser->parse('POINT(3 5)');
		$this->assertEquals('GeoPHP_Point', get_class($point));
		$this->assertEquals(GeoPHP::DEFAULT_SRID, $point->srid);
		$this->assertEquals(3, $point->x);
		$this->assertEquals(5, $point->y);
	}

	public function test_linestring()
	{
		$coords = array(array(3, 5, 1.04, 4), array(-5.55, 3.14, 25.5, 5));
		
		// 2d
		$line = $this->parser->parse('LINESTRING(3 5, -5.55 3.14)');
		$this->assertEquals('GeoPHP_LineString', get_class($line));
		$this->assertEquals(GeoPHP::DEFAULT_SRID, $line->srid);
		$this->assertEquals(2, count($line->points));
		$this->assertEquals(GeoPHP_LineString::from_array($coords), $line);

		// 3dz
		$line = $this->parser->parse('SRID=444;LINESTRING(3 5 1.04, -5.55 3.14 25.5)');
		$this->assertEquals('GeoPHP_LineString', get_class($line));
		$this->assertEquals(GeoPHP_LineString::from_array($coords, 444, true), $line);

		// 3dm
		$line = $this->parser->parse('SRID=444;LINESTRINGM(3 5 1.04, -5.55 3.14 25.5)');
		$this->assertEquals('GeoPHP_LineString', get_class($line));
		$this->assertEquals(GeoPHP_LineString::from_array($coords, 444, false, true), $line);

		// 4d
		$line = $this->parser->parse('SRID=444;LINESTRING(3 5 1.04 4, -5.55 3.14 25.5 5)');
		$this->assertEquals('GeoPHP_LineString', get_class($line));
		$this->assertEquals(GeoPHP_LineString::from_array($coords, 444, true, true), $line);
	}
	
	public function test_polygon()
	{
		$ring1_coords = array(array(0,0,0,4),
		                array(0,5,1,3),
		                array(5,5,2,2),
		                array(5,0,1,1),
		                array(0,0,0,4));
		$ring2_coords = array(array(1,1,0,-2),
		                array(1,4,1,-3),
		                array(4,4,2,-4),
		                array(4,1,1,-5),
		                array(1,1,0,-2));
		                
		// 2d
		$poly = $this->parser->parse('SRID=444;POLYGON((0 0, 0 5, 5 5, 5 0, 0 0),(1 1, 1 4, 4 4, 4 1, 1 1))');
		$this->assertEquals('GeoPHP_Polygon', get_class($poly));
		$this->assertEquals(444, $poly->srid);
		$this->assertEquals(GeoPHP_Polygon::from_array(array($ring1_coords, $ring2_coords), 444), $poly);
		                
		// 3dz
		$poly = $this->parser->parse('SRID=444;POLYGON((0 0 0, 0 5 1, 5 5 2, 5 0 1, 0 0 0),(1 1 0, 1 4 1, 4 4 2, 4 1 1, 1 1 0))');
		$this->assertEquals('GeoPHP_Polygon', get_class($poly));
		$this->assertEquals(GeoPHP_Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true), $poly);
		                
		// 3dm
		$poly = $this->parser->parse('SRID=444;POLYGONM((0 0 0, 0 5 1, 5 5 2, 5 0 1, 0 0 0),(1 1 0, 1 4 1, 4 4 2, 4 1 1, 1 1 0))');
		$this->assertEquals('GeoPHP_Polygon', get_class($poly));
		$this->assertEquals(GeoPHP_Polygon::from_array(array($ring1_coords, $ring2_coords), 444, false, true), $poly);
		                
		// 4d
		$poly = $this->parser->parse('SRID=444;POLYGON((0 0 0 4, 0 5 1 3, 5 5 2 2, 5 0 1 1, 0 0 0 4),(1 1 0 -2, 1 4 1 -3, 4 4 2 -4, 4 1 1 -5, 1 1 0 -2))');
		$this->assertEquals('GeoPHP_Polygon', get_class($poly));
		$this->assertEquals(GeoPHP_Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true, true), $poly);
	}
	
	public function test_multipoint()
	{
		$coords = array(array(3, 5, 1.04, 4), array(-5.55, 3.14, 25.5, 5));
		
		// 2d
		$line = $this->parser->parse('MULTIPOINT( (3 5 ), ( -5.55 3.14) )');
		$this->assertEquals('GeoPHP_MultiPoint', get_class($line));
		$this->assertEquals(GeoPHP::DEFAULT_SRID, $line->srid);
		$this->assertEquals(2, count($line->points));
		$this->assertEquals(GeoPHP_MultiPoint::from_array($coords), $line);

		// 3dz
		$line = $this->parser->parse('SRID=444;MULTIPOINT( (3 5 1.04 ), ( -5.55 3.14  25.5) )');
		$this->assertEquals('GeoPHP_MultiPoint', get_class($line));
		$this->assertEquals(444, $line->srid);
		$this->assertEquals(GeoPHP_MultiPoint::from_array($coords, 444, true), $line);

		// 3dm
		$line = $this->parser->parse('SRID=444;MULTIPOINTM( (3 5 1.04 ), ( -5.55 3.14  25.5) )');
		$this->assertEquals('GeoPHP_MultiPoint', get_class($line));
		$this->assertEquals(GeoPHP_MultiPoint::from_array($coords, 444, false, true), $line);

		// 4d
		$line = $this->parser->parse('SRID=444;MULTIPOINT((3 5 1.04 4),(-5.55 3.14 25.5 5))');
		$this->assertEquals('GeoPHP_MultiPoint', get_class($line));
		$this->assertEquals(GeoPHP_MultiPoint::from_array($coords, 444, true, true), $line);
		
		// 3dz - PostGIS format
		$line = $this->parser->parse('SRID=444;MULTIPOINT(3 5 1.04, -5.55 3.14 25.5)');
		$this->assertEquals('GeoPHP_MultiPoint', get_class($line));
		$this->assertEquals(444, $line->srid);
		$this->assertEquals(GeoPHP_MultiPoint::from_array($coords, 444, true), $line);
	}
	
	public function test_multilinestring()
	{
		$line1_coords = array(array(0,0,0,4),
		                      array(0,5,1,3),
		                      array(5,5,2,2),
		                      array(5,0,1,1),
		                      array(0,0,0,4));
		$line2_coords = array(array(1,1,0,-2),
		                      array(1,4,1,-3),
		                      array(4,4,2,-4),
		                      array(4,1,1,-5),
		                      array(1,1,0,-2));
		                
		// 2d
		$line = $this->parser->parse('SRID=444;MULTILINESTRING((0 0, 0 5, 5 5, 5 0, 0 0), (1 1, 1 4, 4 4, 4 1, 1 1))');
		$this->assertEquals('GeoPHP_MultiLineString', get_class($line));
		$this->assertEquals(444, $line->srid);
		$this->assertEquals(GeoPHP_MultiLineString::from_array(array($line1_coords, $line2_coords), 444), $line);
		                
		// 3dz
		$line = $this->parser->parse('SRID=444;MULTILINESTRING ((0 0 0  , 0 5 1, 5 5 2 , 5 0 1, 0  0 0),(1 1 0 , 1 4 1, 4 4 2, 4 1 1, 1 1 0) ) ');
		$this->assertEquals('GeoPHP_MultiLineString', get_class($line));
		$this->assertEquals(GeoPHP_MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true), $line);
		                
		// 3dm
		$line = $this->parser->parse('SRID=444;MULTILINESTRINGM ((0 0 0  , 0 5 1, 5 5 2 , 5 0 1, 0  0 0),(1 1 0 , 1 4 1, 4 4 2, 4 1 1, 1 1 0) ) ');
		$this->assertEquals('GeoPHP_MultiLineString', get_class($line));
		$this->assertEquals(GeoPHP_MultiLineString::from_array(array($line1_coords, $line2_coords), 444, false, true), $line);
		                
		// 4d
		$line = $this->parser->parse('SRID=444;MULTILINESTRING((0 0 0 4, 0 5 1 3, 5 5 2 2, 5 0 1 1, 0 0 0 4),(1 1 0 -2, 1 4 1 -3, 4 4 2 -4, 4 1 1 -5, 1 1 0 -2))');
		$this->assertEquals('GeoPHP_MultiLineString', get_class($line));
		$this->assertEquals(GeoPHP_MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true, true), $line);
	}
	
	public function test_multipolygon()
	{
		$ring1_coords = array(array(0,0,0,4),
		                array(0,5,1,3),
		                array(5,5,2,2),
		                array(5,0,1,1),
		                array(0,0,0,4));
		$ring2_coords = array(array(1,1,0,4),
		                array(1,4,1,3),
		                array(4,4,2,2),
		                array(4,1,1,1),
		                array(1,1,0,4));
		$ring3_coords = array(array(6,6,0,4),
		                array(6,10,1,3),
		                array(10,10,2,2),
		                array(10,6,1,1),
		                array(6,6,0,4));
		
		// 2d
		$poly = $this->parser->parse('SRID=444;MULTIPOLYGON(((0 0, 0 5, 5 5, 5 0, 0 0),(1 1, 1 4, 4 4, 4 1, 1 1)),((6 6, 6 10, 10 10, 10 6, 6 6)))');
		$this->assertEquals('GeoPHP_MultiPolygon', get_class($poly));
		$this->assertEquals(GeoPHP_MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444), $poly);

		// 3dz
		$poly = $this->parser->parse('SRID=444;MULTIPOLYGON(((0 0 0, 0 5 1, 5 5 2, 5 0 1, 0 0 0),(1 1 0, 1 4 1, 4 4 2, 4 1 1, 1 1 0)),((6 6 0, 6 10 1, 10 10 2, 10 6 1, 6 6 0)))');
		$this->assertEquals('GeoPHP_MultiPolygon', get_class($poly));
		$this->assertEquals(GeoPHP_MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true), $poly);

		// 3dm
		$poly = $this->parser->parse('SRID=444;MULTIPOLYGONM(((0 0 0, 0 5 1, 5 5 2, 5 0 1, 0 0 0),(1 1 0, 1 4 1, 4 4 2, 4 1 1, 1 1 0)),((6 6 0, 6 10 1, 10 10 2, 10 6 1, 6 6 0)))');
		$this->assertEquals('GeoPHP_MultiPolygon', get_class($poly));
		$this->assertEquals(GeoPHP_MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, false, true), $poly);
		
		// 4d
		$poly = $this->parser->parse('SRID=444;MULTIPOLYGON(((0 0 0 4, 0 5 1 3, 5 5 2 2, 5 0 1 1, 0 0 0 4),(1 1 0 4, 1 4 1 3, 4 4 2 2, 4 1 1 1, 1 1 0 4)),((6 6 0 4, 6 10 1 3, 10 10 2 2, 10 6 1 1, 6 6 0 4)))');
		$this->assertEquals('GeoPHP_MultiPolygon', get_class($poly));
		$this->assertEquals(GeoPHP_MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true, true), $poly);
	}
	
	public function test_geometrycollection()
	{
		// 2d point and linestring
		$coll = $this->parser->parse('SRID=444;GEOMETRYCOLLECTION(POINT(4 -5), LINESTRING(1.1 2.2, 3.3 4.4))');
		$this->assertEquals('GeoPHP_GeometryCollection', get_class($coll));
		$this->assertEquals(GeoPHP_GeometryCollection::from_geometries(array(GeoPHP_Point::from_xy(4, -5, 444), GeoPHP_LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)), 444)), 444), $coll);

		// 3dm
		$coll = $this->parser->parse('SRID=444;GEOMETRYCOLLECTIONM(POINT(4 -5 3), LINESTRING(1.1 2.2 3, 3.3 4.4 3))');
		$this->assertEquals('GeoPHP_GeometryCollection', get_class($coll));
		$this->assertEquals(GeoPHP_GeometryCollection::from_geometries(array(GeoPHP_Point::from_xym(4, -5, 3, 444), GeoPHP_LineString::from_array(array(array(1.1, 2.2, 3), array(3.3, 4.4, 3)), 444, false, true)), 444, false, true), $coll);
		$this->assertEquals(444, $coll->geometries[1]->srid);
		$this->assertTrue($coll->geometries[0]->with_m);
	}
}	
?>