<?php
namespace GeoPHP;

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../lib/GeoPHP.php';

class GMLTest extends \PHPUnit_Framework_TestCase
{
	public function test_point_gml()
	{
		$p = Point::from_xy(1.1, 2.2, 444);
		$this->assertEquals('<gml:Point srsName="EPSG:444"><gml:coordinates>1.1,2.2</gml:coordinates></gml:Point>', $p->to_gml());

		$p = Point::from_xyz(1.1, 2.2, 3.3, 444);
		$this->assertEquals('<gml:Point srsName="EPSG:444"><gml:coordinates>1.1,2.2,3.3</gml:coordinates></gml:Point>', $p->to_gml());
	
		$p = Point::from_xym(1.1, 2.2, 3.3, 444);
		$this->assertEquals('<gml:Point srsName="EPSG:444"><gml:coordinates>1.1,2.2,3.3</gml:coordinates></gml:Point>', $p->to_gml());

		$p = Point::from_xyzm(1.1, 2.2, 3.3, 4.4, 444);
		$this->assertEquals('<gml:Point srsName="EPSG:444"><gml:coordinates>1.1,2.2</gml:coordinates></gml:Point>', $p->to_gml());
	}
}
