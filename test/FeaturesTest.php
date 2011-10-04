<?php
namespace GeoPHP;

require_once dirname(__FILE__).'/../lib/GeoPHP.php';

class FeaturesTest extends \PHPUnit_Framework_TestCase
{
	public function test_point_create()
	{
		$p = new Point;
		$this->assertEquals(DEFAULT_SRID, $p->srid);
		$this->assertEquals(false, $p->with_z);
		$this->assertEquals(false, $p->with_m);
		$this->assertEquals(0, $p->x);
		$this->assertEquals(0, $p->y);
		$this->assertEquals(0, $p->z);
		$this->assertEquals(0, $p->m);
		
		$p = new Point(444);
		$this->assertEquals(444, $p->srid);
		
		// from_xy
		$p = Point::from_xy(1.1, -2.2, 444);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(false, $p->with_z);
		$this->assertEquals(false, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(-2.2, $p->y);
		$this->assertEquals(0, $p->z);
		$this->assertEquals(0, $p->m);
		$this->assertEquals(1.1, $p->lon);
		$this->assertEquals(-2.2, $p->lat);
		
		// from_xyz
		$p = Point::from_xyz(1.1, -2.2, 3.3, 444);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(true, $p->with_z);
		$this->assertEquals(false, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(-2.2, $p->y);
		$this->assertEquals(3.3, $p->z);
		$this->assertEquals(0, $p->m);
		
		// from_xym
		$p = Point::from_xym(1.1, -2.2, 3.3, 444);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(false, $p->with_z);
		$this->assertEquals(true, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(-2.2, $p->y);
		$this->assertEquals(0, $p->z);
		$this->assertEquals(3.3, $p->m);
		
		// from_xyzm
		$p = Point::from_xyzm(1.1, 2.2, 3.3, -4.4, 444);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(true, $p->with_z);
		$this->assertEquals(true, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(2.2, $p->y);
		$this->assertEquals(3.3, $p->z);
		$this->assertEquals(-4.4, $p->m);
		
		// from_coordinates
		$coords = array(1.1, 2.2, 3.3, 4.4);
		
		$p = Point::from_array($coords);
		$this->assertEquals(DEFAULT_SRID, $p->srid);
		$this->assertEquals(false, $p->with_z);
		$this->assertEquals(false, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(2.2, $p->y);
		$this->assertEquals(0, $p->z);
		$this->assertEquals(0, $p->m);
		
		$p = Point::from_array($coords, 444, true);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(true, $p->with_z);
		$this->assertEquals(false, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(2.2, $p->y);
		$this->assertEquals(3.3, $p->z);
		$this->assertEquals(0, $p->m);
		
		$p = Point::from_array($coords, 444, false, true);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(false, $p->with_z);
		$this->assertEquals(true, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(2.2, $p->y);
		$this->assertEquals(0, $p->z);
		$this->assertEquals(3.3, $p->m);
		
		$p = Point::from_array($coords, 444, true, true);
		$this->assertEquals(444, $p->srid);
		$this->assertEquals(true, $p->with_z);
		$this->assertEquals(true, $p->with_m);
		$this->assertEquals(1.1, $p->x);
		$this->assertEquals(2.2, $p->y);
		$this->assertEquals(3.3, $p->z);
		$this->assertEquals(4.4, $p->m);
	}
	
	public function test_point_ewkb()
	{
		$p = Point::from_xy(1.1, 2.2, 444);
		$this->assertEquals('0101000020BC0100009A9999999999F13F9A99999999990140', $p->to_hexewkb());

		$p = Point::from_xyz(1.1, 2.2, 3.3, 444);
		$this->assertEquals('01010000A0BC0100009A9999999999F13F9A999999999901406666666666660A40', $p->to_hexewkb());

		// Not sure how to get EWKB for this
		//$p = Point::from_xym(1.1, 2.2, 3.3, 444);

		$p = Point::from_xyzm(1.1, 2.2, 3.3, 4.4, 444);
		$this->assertEquals('01010000E0BC0100009A9999999999F13F9A999999999901406666666666660A409A99999999991140', $p->to_hexewkb());
	}
	
	public function test_point_ewkt()
	{
		$p = Point::from_xy(1.1, 2.2, 444);
		$this->assertEquals('SRID=444;POINT(1.1 2.2)', $p->to_ewkt());

		$p = Point::from_xyz(1.1, 2.2, 3.3, 444);
		$this->assertEquals('SRID=444;POINT(1.1 2.2 3.3)', $p->to_ewkt());
		$this->assertEquals('POINT(1.1 2.2 3.3)', $p->to_ewkt(false));
		$this->assertEquals('POINT(1.1 2.2)', $p->to_wkt());
		
		$p = Point::from_xym(1.1, 2.2, 3.3, 444);
		$this->assertEquals('SRID=444;POINTM(1.1 2.2 3.3)', $p->to_ewkt(true, false));

		$p = Point::from_xyzm(1.1, 2.2, 3.3, 4.4, 444);
		$this->assertEquals('SRID=444;POINT(1.1 2.2 3.3 4.4)', $p->to_ewkt());
	}
	
	public function test_point_extent()
	{
		$p = Point::from_xy(1.1, 2.2, 444);
		$env = $p->extent();
		$this->assertEquals($env->ll, $env->ur);
		$this->assertEquals($p->x, $env->ll->x);
		$this->assertEquals($p->y, $env->ll->y);
		$this->assertEquals(false, $env->with_z);

		$p = Point::from_xyz(1.1, 2.2, 3.3, 444);
		$env = $p->extent();
		$this->assertEquals($env->ll, $env->ur);
		$this->assertEquals($p->x, $env->ll->x);
		$this->assertEquals($p->y, $env->ll->y);
		$this->assertEquals($p->z, $env->ll->z);
		$this->assertEquals(true, $env->with_z);
		
		$this->assertEquals($p->x, $env->left);
		$this->assertEquals($p->x, $env->right);
		$this->assertEquals($p->y, $env->top);
		$this->assertEquals($p->y, $env->bottom);
	}
	
	public function test_multipoint_create()
	{
		$coords = array(array(1.1, 2.2, -3.3, 0), array(3.3, 4.4, 25, -10));

		$mp = MultiPoint::from_array($coords, 444);
		$this->assertTrue($mp instanceof MultiPoint);
		$this->assertEquals(2, count($mp->points));
		$this->assertEquals(1.1, $mp->points[0]->x);
		$this->assertEquals(2.2, $mp->points[0]->y);
		$this->assertEquals(3.3, $mp->points[1]->x);
		$this->assertEquals(4.4, $mp->points[1]->y);

		$mp = MultiPoint::from_array($coords, 444, true);
		$this->assertTrue($mp instanceof MultiPoint);
		$this->assertEquals(1.1, $mp->points[0]->x);
		$this->assertEquals(2.2, $mp->points[0]->y);
		$this->assertEquals(-3.3, $mp->points[0]->z);

		$mp = MultiPoint::from_array($coords, 444, true, true);
		$this->assertTrue($mp instanceof MultiPoint);
		$this->assertEquals(3.3, $mp->points[1]->x);
		$this->assertEquals(4.4, $mp->points[1]->y);
		$this->assertEquals(25, $mp->points[1]->z);
		$this->assertEquals(-10, $mp->points[1]->m);
	}
	
	public function test_multipoint_ewkb()
	{
		$coords = array(array(1.1, 2.2, -10), array(3.3, 4.4, 2.55555555));

		$mp = MultiPoint::from_array($coords, 444);
		$this->assertEquals('0104000020BC0100000200000001010000009A9999999999F13F9A9999999999014001010000006666666666660A409A99999999991140', $mp->to_hexewkb());

		$mp = MultiPoint::from_array($coords, 444, true);
		$this->assertEquals('01040000A0BC0100000200000001010000809A9999999999F13F9A9999999999014000000000000024C001010000806666666666660A409A99999999991140EDE3B21BC7710440', $mp->to_hexewkb());
	}
	
	public function test_multipoint_ewkt()
	{
		$mp = MultiPoint::from_array(array(array(1.1, 2.2), array(3.3, 4.4)), 444);
		$this->assertEquals('SRID=444;MULTIPOINT(1.1 2.2,3.3 4.4)', $mp->to_ewkt());
		
		$mp = MultiPoint::from_array(array(array(1.1, 2.2, 3.3), array(4.4, 5.5, 6.6)), 444, true);
		$this->assertEquals('SRID=444;MULTIPOINT(1.1 2.2 3.3,4.4 5.5 6.6)', $mp->to_ewkt());
		$this->assertEquals('MULTIPOINT(1.1 2.2,4.4 5.5)', $mp->to_wkt());
		
		$mp = MultiPoint::from_array(array(array(1.1, 2.2, 3.3, -23), array(4.4, 5.5, 6.6, 0)), 444, true, true);
		$this->assertEquals('SRID=444;MULTIPOINT(1.1 2.2 3.3 -23,4.4 5.5 6.6 0)', $mp->to_ewkt());
	}
	
	
	public function test_linestring_create()
	{
		$coords = array(array(1.1, 2.2, -3.3, 0), array(3.3, 4.4, 25, -10));

		$line = LineString::from_array($coords, 444);
		$this->assertTrue($line instanceof LineString);
		$this->assertEquals(2, count($line->points));
		$this->assertEquals(1.1, $line->points[0]->x);
		$this->assertEquals(2.2, $line->points[0]->y);
		$this->assertEquals(3.3, $line->points[1]->x);
		$this->assertEquals(4.4, $line->points[1]->y);

		$line = LineString::from_array($coords, 444, true);
		$this->assertTrue($line instanceof LineString);
		$this->assertEquals(1.1, $line->points[0]->x);
		$this->assertEquals(2.2, $line->points[0]->y);
		$this->assertEquals(-3.3, $line->points[0]->z);

		$line = LineString::from_array($coords, 444, true, true);
		$this->assertTrue($line instanceof LineString);
		$this->assertEquals(3.3, $line->points[1]->x);
		$this->assertEquals(4.4, $line->points[1]->y);
		$this->assertEquals(25, $line->points[1]->z);
		$this->assertEquals(-10, $line->points[1]->m);
	}
	
	
	public function test_linestring_ewkb()
	{
		$coords = array(array(1.1, 2.2, -10, 5), 
		                array(3.3, 4.4, 2.55555555, -5), 
		                array(0, -0.5, 3.14, 1.111));

		$line = LineString::from_array($coords, 444);
		$this->assertEquals('0102000020BC010000030000009A9999999999F13F9A999999999901406666666666660A409A999999999911400000000000000000000000000000E0BF', $line->to_hexewkb());

		$line = LineString::from_array($coords, 444, true);
		$this->assertEquals('01020000A0BC010000030000009A9999999999F13F9A9999999999014000000000000024C06666666666660A409A99999999991140EDE3B21BC77104400000000000000000000000000000E0BF1F85EB51B81E0940', $line->to_hexewkb());
	}
	
	public function test_linestring_ewkt()
	{
		$coords = array(array(1.1, 2.2, -10, 5), 
		                array(3.3, 4.4, 2.55555555, -5), 
		                array(0, -0.5, 3.14, 1.111));

		$line = LineString::from_array($coords, 444);
		$this->assertEquals('SRID=444;LINESTRING(1.1 2.2,3.3 4.4,0 -0.5)', $line->to_ewkt());
		
		$line = LineString::from_array($coords, 444, true);
		$this->assertEquals('SRID=444;LINESTRING(1.1 2.2 -10,3.3 4.4 2.55555555,0 -0.5 3.14)', $line->to_ewkt());
		$this->assertEquals('LINESTRING(1.1 2.2,3.3 4.4,0 -0.5)', $line->to_wkt());
		
		$line = LineString::from_array($coords, 444, true, true);
		$this->assertEquals('SRID=444;LINESTRING(1.1 2.2 -10 5,3.3 4.4 2.55555555 -5,0 -0.5 3.14 1.111)', $line->to_ewkt());
	}

	public function test_polygon_create()
	{
		$ring1_coords = array(array(0,0,0),
		                array(0,5,1),
		                array(5,5,2),
		                array(5,0,1),
		                array(0,0,0));
		$ring2_coords = array(array(1,1,0),
		                array(1,4,1),
		                array(4,4,2),
		                array(4,1,1),
		                array(1,1,0));
		
		$ring1 = LinearRing::from_array($ring1_coords, 444, true);
		$ring2 = LinearRing::from_array($ring2_coords, 444, true);
		
		$poly = Polygon::from_linear_rings(array($ring1, $ring2), 444, true);
		$this->assertTrue($poly instanceof Polygon);
		$this->assertEquals(2, count($poly->rings));
		$this->assertEquals(5, $poly->rings[0]->points[2]->x);
		$this->assertEquals(5, $poly->rings[0]->points[2]->y);
		$this->assertEquals(2, $poly->rings[0]->points[2]->z);
		
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true);
		$this->assertTrue($poly instanceof Polygon);
		$this->assertEquals(2, count($poly->rings));
	}
	
	public function test_polygon_ewkb()
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
		// No Z value
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444);
		$this->assertEquals('0103000020BC0100000200000005000000000000000000000000000000000000000000000000000000000000000000144000000000000014400000000000001440000000000000144000000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F000000000000F03F0000000000001040000000000000104000000000000010400000000000001040000000000000F03F000000000000F03F000000000000F03F', $poly->to_hexewkb());
		
		// z value
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true);
		$this->assertEquals('01030000A0BC010000020000000500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F00000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F0000000000000000', $poly->to_hexewkb());
		
		// z + m
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true, true);
		$this->assertEquals('01030000E0BC0100000200000005000000000000000000000000000000000000000000000000000000000000000000104000000000000000000000000000001440000000000000F03F0000000000000840000000000000144000000000000014400000000000000040000000000000004000000000000014400000000000000000000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000000000000000104005000000000000000000F03F000000000000F03F000000000000000000000000000000C0000000000000F03F0000000000001040000000000000F03F00000000000008C000000000000010400000000000001040000000000000004000000000000010C00000000000001040000000000000F03F000000000000F03F00000000000014C0000000000000F03F000000000000F03F000000000000000000000000000000C0', $poly->to_hexewkb());

		// m
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, false, true);
		$this->assertEquals('0103000060BC010000020000000500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F00000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F0000000000000000', $poly->to_hexewkb());
	}
	
	public function test_polygon_ewkt()
	{
		$ring1_coords = array(array(0,0,0),
		                array(0,5,1),
		                array(5,5,2),
		                array(5,0,1),
		                array(0,0,0));
		$ring2_coords = array(array(1,1,0),
		                array(1,4,1),
		                array(4,4,2),
		                array(4,1,1),
		                array(1,1,0));
		
		$poly = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true);
		$this->assertEquals('SRID=444;POLYGON((0 0 0,0 5 1,5 5 2,5 0 1,0 0 0),(1 1 0,1 4 1,4 4 2,4 1 1,1 1 0))', $poly->to_ewkt());
		$this->assertEquals('POLYGON((0 0,0 5,5 5,5 0,0 0),(1 1,1 4,4 4,4 1,1 1))', $poly->to_wkt());
	}
	
	public function test_multilinestring_create()
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

		$line1 = LineString::from_array($line1_coords, 444, true);
		$line2 = LineString::from_array($line2_coords, 444, true);
		
		$ml = MultiLineString::from_line_strings(array($line1, $line2), 444, true);
		$this->assertTrue($ml instanceof MultiLineString);
		$this->assertEquals(2, count($ml->lines));
		$this->assertEquals(5, $ml->lines[0]->points[2]->x);
		$this->assertEquals(5, $ml->lines[0]->points[2]->y);
		$this->assertEquals(2, $ml->lines[0]->points[2]->z);
		
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true);
		$this->assertTrue($ml instanceof MultiLineString);
		$this->assertEquals(2, count($ml->lines));
	}
	
	public function test_multilinestring_ewkb()
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
		// No Z value
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444);
		$this->assertEquals('0105000020BC010000020000000102000000050000000000000000000000000000000000000000000000000000000000000000001440000000000000144000000000000014400000000000001440000000000000000000000000000000000000000000000000010200000005000000000000000000F03F000000000000F03F000000000000F03F0000000000001040000000000000104000000000000010400000000000001040000000000000F03F000000000000F03F000000000000F03F', $ml->to_hexewkb());
		
		// z value
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true);
		$this->assertEquals('01050000A0BC0100000200000001020000800500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F000000000000000000000000000000000000000000000000010200008005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F0000000000000000', $ml->to_hexewkb());
		
		// m
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, false, true);
		$this->assertEquals('0105000060BC0100000200000001020000400500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F000000000000000000000000000000000000000000000000010200004005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F0000000000000000', $ml->to_hexewkb());

		// z + m
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true, true);
		$this->assertEquals('01050000E0BC0100000200000001020000C005000000000000000000000000000000000000000000000000000000000000000000104000000000000000000000000000001440000000000000F03F0000000000000840000000000000144000000000000014400000000000000040000000000000004000000000000014400000000000000000000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000000000000000104001020000C005000000000000000000F03F000000000000F03F000000000000000000000000000000C0000000000000F03F0000000000001040000000000000F03F00000000000008C000000000000010400000000000001040000000000000004000000000000010C00000000000001040000000000000F03F000000000000F03F00000000000014C0000000000000F03F000000000000F03F000000000000000000000000000000C0', $ml->to_hexewkb());
	}
	
	public function test_multilinestring_ewkt()
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
		// No Z value
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444);
		$this->assertEquals('SRID=444;MULTILINESTRING((0 0,0 5,5 5,5 0,0 0),(1 1,1 4,4 4,4 1,1 1))', $ml->to_ewkt());
		
		// z value
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true);
		$this->assertEquals('SRID=444;MULTILINESTRING((0 0 0,0 5 1,5 5 2,5 0 1,0 0 0),(1 1 0,1 4 1,4 4 2,4 1 1,1 1 0))', $ml->to_ewkt());
		
		// m
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, false, true);
		$this->assertEquals('SRID=444;MULTILINESTRINGM((0 0 0,0 5 1,5 5 2,5 0 1,0 0 0),(1 1 0,1 4 1,4 4 2,4 1 1,1 1 0))', $ml->to_ewkt());

		// z + m
		$ml = MultiLineString::from_array(array($line1_coords, $line2_coords), 444, true, true);
		$this->assertEquals('SRID=444;MULTILINESTRING((0 0 0 4,0 5 1 3,5 5 2 2,5 0 1 1,0 0 0 4),(1 1 0 -2,1 4 1 -3,4 4 2 -4,4 1 1 -5,1 1 0 -2))', $ml->to_ewkt());
	}
	
	public function test_multipolygon_create()
	{
		$ring1_coords = array(array(0,0,0),
		                array(0,5,1),
		                array(5,5,2),
		                array(5,0,1),
		                array(0,0,0));
		$ring2_coords = array(array(1,1,0),
		                array(1,4,1),
		                array(4,4,2),
		                array(4,1,1),
		                array(1,1,0));
		$ring3_coords = array(array(6,6,0),
		                array(6,10,1),
		                array(10,10,2),
		                array(10,6,1),
		                array(6,6,0));
		
		$poly1 = Polygon::from_array(array($ring1_coords, $ring2_coords), 444, true);
		$poly2 = Polygon::from_array(array($ring3_coords), 444, true);
		
		$mp = MultiPolygon::from_polygons(array($poly1, $poly2), 444, true);
		$this->assertTrue($mp instanceof MultiPolygon);
		
		$this->assertEquals(2, count($mp->polygons));
		$this->assertEquals(2, count($mp->polygons[0]->rings));
		$this->assertEquals(1, count($mp->polygons[1]->rings));
		$this->assertEquals(10, $mp->polygons[1]->rings[0]->points[2]->x);
		$this->assertEquals(10, $mp->polygons[1]->rings[0]->points[2]->y);
		$this->assertEquals(2, $mp->polygons[1]->rings[0]->points[2]->z);
		
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true);
		$this->assertTrue($mp instanceof MultiPolygon);
		$this->assertEquals(2, count($mp->polygons));
	}
	
	public function test_multipolygon_ewkb()
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
		
		// No Z values
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444);
		$this->assertEquals('0106000020BC0100000200000001030000000200000005000000000000000000000000000000000000000000000000000000000000000000144000000000000014400000000000001440000000000000144000000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F000000000000F03F0000000000001040000000000000104000000000000010400000000000001040000000000000F03F000000000000F03F000000000000F03F010300000001000000050000000000000000001840000000000000184000000000000018400000000000002440000000000000244000000000000024400000000000002440000000000000184000000000000018400000000000001840', $mp->to_hexewkb());

		// Z value
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true);
		$this->assertEquals('01060000A0BC010000020000000103000080020000000500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F00000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F00000000000000000103000080010000000500000000000000000018400000000000001840000000000000000000000000000018400000000000002440000000000000F03F00000000000024400000000000002440000000000000004000000000000024400000000000001840000000000000F03F000000000000184000000000000018400000000000000000', $mp->to_hexewkb());

		// M value
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, false, true);
		$this->assertEquals('0106000060BC010000020000000103000040020000000500000000000000000000000000000000000000000000000000000000000000000000000000000000001440000000000000F03F00000000000014400000000000001440000000000000004000000000000014400000000000000000000000000000F03F00000000000000000000000000000000000000000000000005000000000000000000F03F000000000000F03F0000000000000000000000000000F03F0000000000001040000000000000F03F0000000000001040000000000000104000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F00000000000000000103000040010000000500000000000000000018400000000000001840000000000000000000000000000018400000000000002440000000000000F03F00000000000024400000000000002440000000000000004000000000000024400000000000001840000000000000F03F000000000000184000000000000018400000000000000000', $mp->to_hexewkb());
		
		// Z+M
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true, true);
		$this->assertEquals('01060000E0BC0100000200000001030000C00200000005000000000000000000000000000000000000000000000000000000000000000000104000000000000000000000000000001440000000000000F03F0000000000000840000000000000144000000000000014400000000000000040000000000000004000000000000014400000000000000000000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000000000000000104005000000000000000000F03F000000000000F03F00000000000000000000000000001040000000000000F03F0000000000001040000000000000F03F000000000000084000000000000010400000000000001040000000000000004000000000000000400000000000001040000000000000F03F000000000000F03F000000000000F03F000000000000F03F000000000000F03F0000000000000000000000000000104001030000C00100000005000000000000000000184000000000000018400000000000000000000000000000104000000000000018400000000000002440000000000000F03F0000000000000840000000000000244000000000000024400000000000000040000000000000004000000000000024400000000000001840000000000000F03F000000000000F03F0000000000001840000000000000184000000000000000000000000000001040', $mp->to_hexewkb());
	}
	
	public function test_multipolygon_ewkt()
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
		
		// No Z values
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444);
		$this->assertEquals('SRID=444;MULTIPOLYGON(((0 0,0 5,5 5,5 0,0 0),(1 1,1 4,4 4,4 1,1 1)),((6 6,6 10,10 10,10 6,6 6)))', $mp->to_ewkt());

		// Z value
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true);
		$this->assertEquals('SRID=444;MULTIPOLYGON(((0 0 0,0 5 1,5 5 2,5 0 1,0 0 0),(1 1 0,1 4 1,4 4 2,4 1 1,1 1 0)),((6 6 0,6 10 1,10 10 2,10 6 1,6 6 0)))', $mp->to_ewkt());

		// M value
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, false, true);
		$this->assertEquals('SRID=444;MULTIPOLYGONM(((0 0 0,0 5 1,5 5 2,5 0 1,0 0 0),(1 1 0,1 4 1,4 4 2,4 1 1,1 1 0)),((6 6 0,6 10 1,10 10 2,10 6 1,6 6 0)))', $mp->to_ewkt());
		
		// Z+M
		$mp = MultiPolygon::from_array(array(array($ring1_coords, $ring2_coords), array($ring3_coords)), 444, true, true);
		$this->assertEquals('SRID=444;MULTIPOLYGON(((0 0 0 4,0 5 1 3,5 5 2 2,5 0 1 1,0 0 0 4),(1 1 0 4,1 4 1 3,4 4 2 2,4 1 1 1,1 1 0 4)),((6 6 0 4,6 10 1 3,10 10 2 2,10 6 1 1,6 6 0 4)))', $mp->to_ewkt());
	}
	
	public function test_geometrycollection_create()
	{
		$coll = new GeometryCollection(444);
		$point = Point::from_xy(4, -5);
		$coll->geometries[] = $point;
		$this->assertEquals(1, count($coll->geometries));
		$this->assertEquals(Point::from_xy(4, -5), $coll->geometries[0]);
		
		$line = LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)));
		$coll->geometries[] = $line;
		$this->assertEquals(2, count($coll->geometries));
		$this->assertEquals(LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4))), $coll->geometries[1]);
		
		$this->assertEquals(444, $coll->srid);
	}
	
	public function test_geometrycollection_ewkb()
	{
		$coll = GeometryCollection::from_geometries(array(Point::from_xy(4, -5, 444), LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)), 444)), 444);
		$this->assertEquals('0107000020BC010000020000000101000000000000000000104000000000000014C00102000000020000009A9999999999F13F9A999999999901406666666666660A409A99999999991140', $coll->to_hexewkb());

		$coll = GeometryCollection::from_geometries(array(Point::from_xym(4, -5, 3, 444), LineString::from_array(array(array(1.1, 2.2, 3), array(3.3, 4.4, 3)), 444, false, true)), 444, false, true);
		$this->assertEquals('0107000060BC010000020000000101000040000000000000104000000000000014C000000000000008400102000040020000009A9999999999F13F9A9999999999014000000000000008406666666666660A409A999999999911400000000000000840', $coll->to_hexewkb());
	}
	
	public function test_geometrycollection_ewkt()
	{
		$coll = GeometryCollection::from_geometries(array(Point::from_xy(4, -5), LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)))), 444);
		$this->assertEquals('SRID=444;GEOMETRYCOLLECTION(POINT(4 -5),LINESTRING(1.1 2.2,3.3 4.4))', $coll->to_ewkt());

		$coll = GeometryCollection::from_geometries(array(Point::from_xym(4, -5, 3, 444), LineString::from_array(array(array(1.1, 2.2, 3), array(3.3, 4.4, 3)), 444, false, true)), 444, false, true);
		$this->assertEquals('SRID=444;GEOMETRYCOLLECTIONM(POINTM(4 -5 3),LINESTRINGM(1.1 2.2 3,3.3 4.4 3))', $coll->to_ewkt());
	}
	
	public function test_extent()
	{
		$ring1_coords = array(array(0,1,0),
		                array(0,6,1),
		                array(5,6,2),
		                array(5,1,1),
		                array(0,1,0));
		$ring2_coords = array(array(1,2,0),
		                array(1,4,1),
		                array(4,4,2),
		                array(4,2,1),
		                array(1,2,0));
		
		$ring1 = LinearRing::from_array($ring1_coords, 444, true);
		$ring2 = LinearRing::from_array($ring2_coords, 444, true);
		
		$poly = Polygon::from_linear_rings(array($ring1, $ring2), 444, true);
		
		$e = $poly->extent();

		$this->assertTrue($e->ll instanceof Point);
		$this->assertTrue($e->ur instanceof Point);
		
		$this->assertEquals(0, $e->ll->x);
		$this->assertEquals(1, $e->ll->y);
		$this->assertEquals(0, $e->ll->z);
		$this->assertEquals(5, $e->ur->x);
		$this->assertEquals(6, $e->ur->y);
		$this->assertEquals(2, $e->ur->z);
	}
}
?>