<?php
require_once dirname(__FILE__).'/Geometry.php';

class GeoPHP_LineString extends GeoPHP_Geometry
{
	public $points;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		parent::__construct($srid, $with_z, $with_m);
		$this->binary_type = 2;
		$this->text_type = 'LINESTRING';
		
		$this->points = array();
	}
	
	public function binary_representation($allow_z = true, $allow_m = true)
	{
        $rep = pack('V', count($this->points));
        foreach ($this->points as $point)
        {
        	$rep .= $point->binary_representation($allow_z, $allow_m);
        }
        return $rep;
	}
	
	public function text_representation($allow_z = true, $allow_m = true)
	{
		return implode(',', array_map(create_function('$p', 'return $p->text_representation('.intval($allow_z).','.intval($allow_m).');'), $this->points));
	}

	public static function from_points($points, $srid = null, $with_z = false, $with_m = false)
	{
		$line = new GeoPHP_LineString($srid, $with_z, $with_m);
		$line->points = $points;
		return $line;
	}
	
	public static function from_array($points, $srid = null, $with_z = false, $with_m = false)
	{
		$line = new GeoPHP_LineString($srid, $with_z, $with_m);
		foreach ($points as $point)
		{
			$line->points[] = GeoPHP_Point::from_array($point, $srid, $with_z, $with_m);
		}
		return $line;
	}
}
?>
