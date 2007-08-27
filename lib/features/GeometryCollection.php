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
