<?php
namespace GeoPHP\Tests\Feature;

use GeoPHP\Feature\Point;
use GeoPHP\Feature\Polygon;
use GeoPHP\Feature\LinearRing;

class GeometryTest extends \PHPUnit_Framework_TestCase
{
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