<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\Polygon;
use GeoPHP\Feature\LinearRing;

class PolygonTest extends \PHPUnit_Framework_TestCase
{
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

}
?>