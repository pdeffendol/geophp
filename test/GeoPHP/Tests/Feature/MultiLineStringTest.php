<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\LineString;
use GeoPHP\Feature\MultiLineString;

class MultiLineStringTest extends \PHPUnit_Framework_TestCase
{
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
}
?>