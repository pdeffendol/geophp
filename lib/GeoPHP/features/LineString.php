<?php
namespace GeoPHP;

class LineString extends Geometry
{
	public $points;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		parent::__construct($srid, $with_z, $with_m);
		$this->binary_type = 2;
		$this->text_type = 'LINESTRING';
		
		$this->points = array();
	}
	
	public function bounding_box()
	{
		$max_x = -INF;
		$max_y = -INF;
		$min_x = INF;
		$min_y = INF;
		
		if (!$this->with_z)
		{
			foreach ($this->points as $p)
			{
				if ($p->x < $min_x) $min_x = $p->x;
				if ($p->y < $min_y) $min_y = $p->y;
				if ($p->x > $max_x) $max_x = $p->x;
				if ($p->y > $max_y) $max_y = $p->y;
			}
			return array(
				Point::from_xy($min_x, $min_y),
				Point::from_xy($max_x, $max_y)
			);
		}
		else
		{
			$max_z = -INF;
			$min_z = INF;			

			foreach ($this->points as $p)
			{
				if ($p->x < $min_x) $min_x = $p->x;
				if ($p->y < $min_y) $min_y = $p->y;
				if ($p->z < $min_z) $min_z = $p->z;
				if ($p->x > $max_x) $max_x = $p->x;
				if ($p->y > $max_y) $max_y = $p->y;
				if ($p->z > $max_z) $max_z = $p->z;
			}
			return array(
				Point::from_xyz($min_x, $min_y, $min_z),
				Point::from_xyz($max_x, $max_y, $max_z)
			);
		}
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
		$line = new LineString($srid, $with_z, $with_m);
		$line->points = $points;
		return $line;
	}
	
	public static function from_array($points, $srid = null, $with_z = false, $with_m = false)
	{
		$line = new LineString($srid, $with_z, $with_m);
		foreach ($points as $point)
		{
			$line->points[] = Point::from_array($point, $srid, $with_z, $with_m);
		}
		return $line;
	}
}
?>
