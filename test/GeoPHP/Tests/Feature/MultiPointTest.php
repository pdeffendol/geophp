<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\MultiPoint;

class MultiPointTest extends \PHPUnit_Framework_TestCase
{
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
}
