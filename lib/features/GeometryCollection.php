<?php
require_once dirname(__FILE__).'/Geometry.php';

class GeoPHP_GeometryCollection extends GeoPHP_Geometry
{
	public $geometries;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		parent::__construct($srid, $with_z, $with_m);
		$this->binary_type = 7;
		$this->text_type = 'GEOMETRYCOLLECTION';
		
		$this->geometries = array();
	}
	
	public function bounding_box()
	{
		$max_x = -INF;
		$max_y = -INF;
		$min_x = INF;
		$min_y = INF;
		
		if (!$this->with_z)
		{
			foreach ($this->geometries as $geom)
			{
				$bbox = $geom->bounding_box();
				$ll = $bbox[0];
				$ur = $bbox[1];
				
				if ($ll->x < $min_x) $min_x = $ll->x;
				if ($ll->y < $min_y) $min_y = $ll->y;
				if ($ur->x > $max_x) $max_x = $ur->x;
				if ($ur->y > $max_y) $max_y = $ur->y;
			}
			return array(
				GeoPHP_Point::from_xy($min_x, $min_y),
				GeoPHP_Point::from_xy($max_x, $max_y)
			);
		}
		else
		{
			$max_z = INF;
			$min_z = -INF;			

			foreach ($this->geometries as $geom)
			{
				$bbox = $geom->bounding_box();
				$ll = $bbox[0];
				$ur = $bbox[1];
				
				if ($ll->x < $min_x) $min_x = $ll->x;
				if ($ll->y < $min_y) $min_y = $ll->y;
				if ($ll->z < $min_z) $min_z = $ll->z;
				if ($ur->x > $max_x) $max_x = $ur->x;
				if ($ur->y > $max_y) $max_y = $ur->y;
				if ($ur->z > $max_z) $max_y = $ur->z;
			}
			return array(
				GeoPHP_Point::from_xy($min_x, $min_y),
				GeoPHP_Point::from_xy($max_x, $max_y)
			);
		}
	}
	
	public function binary_representation($allow_z = true, $allow_m = true)
	{
        $rep = pack('V', count($this->geometries));
        foreach ($this->geometries as $geometry)
        {
        	$rep .= $geometry->to_ewkb(false, $allow_z, $allow_m);
        }
        return $rep;
	}
	
	public function text_representation($allow_z = true, $allow_m = true)
	{
		return implode(',', array_map(create_function('$geom', 'return $geom->to_ewkt(false, '.intval($allow_z).', '.intval($allow_m).');'), $this->geometries));
	}

	public static function from_geometries($geometries, $srid = null, $with_z = false, $with_m = false)
	{
		$coll = new GeoPHP_GeometryCollection($srid, $with_z, $with_m);
		$coll->geometries = $geometries;
		return $coll;
	}
}
?>
