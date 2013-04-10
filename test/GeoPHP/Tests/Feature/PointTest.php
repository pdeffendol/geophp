<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\Point;
use GeoPHP\Constants;

class PointTest extends \PHPUnit_Framework_TestCase
{
  public function test_point_create()
  {
    $p = new Point;
    $this->assertEquals(Constants::DEFAULT_SRID, $p->srid);
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
    $this->assertEquals(Constants::DEFAULT_SRID, $p->srid);
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
}
?>