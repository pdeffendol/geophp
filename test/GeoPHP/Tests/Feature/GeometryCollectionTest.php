<?php
// namespace GeoPHP\Tests\Feature;

// use GeoPHP\Feature\GeometryCollection;
// use GeoPHP\Feature\Point;
// use GeoPHP\Feature\LineString;

// class GeometryCollectionTest extends \PHPUnit_Framework_TestCase
// {
//   public function test_geometrycollection_create()
//   {
//     $coll = new GeometryCollection(444);
//     $point = Point::from_xy(4, -5);
//     $coll->geometries[] = $point;
//     $this->assertEquals(1, count($coll->geometries));
//     $this->assertEquals(Point::from_xy(4, -5), $coll->geometries[0]);

//     $line = LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)));
//     $coll->geometries[] = $line;
//     $this->assertEquals(2, count($coll->geometries));
//     $this->assertEquals(LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4))), $coll->geometries[1]);

//     $this->assertEquals(444, $coll->srid);
//   }

//   public function test_geometrycollection_ewkb()
//   {
//     $coll = GeometryCollection::from_geometries(array(Point::from_xy(4, -5, 444), LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)), 444)), 444);
//     $this->assertEquals('0107000020BC010000020000000101000000000000000000104000000000000014C00102000000020000009A9999999999F13F9A999999999901406666666666660A409A99999999991140', $coll->to_hexewkb());

//     $coll = GeometryCollection::from_geometries(array(Point::from_xym(4, -5, 3, 444), LineString::from_array(array(array(1.1, 2.2, 3), array(3.3, 4.4, 3)), 444, false, true)), 444, false, true);
//     $this->assertEquals('0107000060BC010000020000000101000040000000000000104000000000000014C000000000000008400102000040020000009A9999999999F13F9A9999999999014000000000000008406666666666660A409A999999999911400000000000000840', $coll->to_hexewkb());
//   }

//   public function test_geometrycollection_ewkt()
//   {
//     $coll = GeometryCollection::from_geometries(array(Point::from_xy(4, -5), LineString::from_array(array(array(1.1, 2.2), array(3.3, 4.4)))), 444);
//     $this->assertEquals('SRID=444;GEOMETRYCOLLECTION(POINT(4 -5),LINESTRING(1.1 2.2,3.3 4.4))', $coll->to_ewkt());

//     $coll = GeometryCollection::from_geometries(array(Point::from_xym(4, -5, 3, 444), LineString::from_array(array(array(1.1, 2.2, 3), array(3.3, 4.4, 3)), 444, false, true)), 444, false, true);
//     $this->assertEquals('SRID=444;GEOMETRYCOLLECTIONM(POINTM(4 -5 3),LINESTRINGM(1.1 2.2 3,3.3 4.4 3))', $coll->to_ewkt());
//   }
// }
