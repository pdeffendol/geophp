<?php
require_once dirname(__FILE__).'/LineString.php';

class GeoPHP_LinearRing extends GeoPHP_LineString
{
	// Really just a LineString that is supposed to be closed.  Not checking
	// for validity right now.

	public static function from_points($points, $srid = null, $with_z = false, $with_m = false)
	{
		$line = new GeoPHP_LinearRing($srid, $with_z, $with_m);
		$line->points = $points;
		return $line;
	}
	
	public static function from_array($points, $srid = null, $with_z = false, $with_m = false)
	{
		$line = new GeoPHP_LinearRing($srid, $with_z, $with_m);
		foreach ($points as $point)
		{
			$line->points[] = GeoPHP_Point::from_array($point, $srid, $with_z, $with_m);
		}
		return $line;
	}
}
?>
