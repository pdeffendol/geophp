<?php
require_once dirname(__FILE__).'/Geometry.php';

class GeoPHP_Polygon extends GeoPHP_Geometry
{
	public $rings;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		parent::__construct($srid, $with_z, $with_m);
		$this->binary_type = 3;
		$this->text_type = 'POLYGON';
		$this->rings = array();
	}
	
	public function binary_representation($allow_z = true, $allow_m = true)
	{
        $rep = pack('V', count($this->rings));
        foreach ($this->rings as $ring)
        {
        	$rep .= $ring->binary_representation($allow_z, $allow_m);
        }
        return $rep;
	}
	
	public function text_representation($allow_z = true, $allow_m = true)
	{
		return implode(',', array_map(create_function('$ring', 'return "(".$ring->text_representation('.intval($allow_z).', '.intval($allow_m).').")";'), $this->rings));
	}

	public static function from_linear_rings($rings, $srid = null, $with_z = false, $with_m = false)
	{
		$poly = new GeoPHP_Polygon($srid, $with_z, $with_m);
		$poly->rings = $rings;
		return $poly;
	}

	public static function from_array($point_sets, $srid = null, $with_z = false, $with_m = false)
	{
		$poly = new GeoPHP_Polygon($srid, $with_z, $with_m);
		foreach ($point_sets as $set)
		{
			$poly->rings[] = GeoPHP_LinearRing::from_array($set, $srid, $with_z, $with_m);
		}
		return $poly;
	}
}
?>
