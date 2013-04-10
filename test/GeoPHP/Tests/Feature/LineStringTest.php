<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\LineString;

class LineStringTest extends \PHPUnit_Framework_TestCase
{
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

}
?>