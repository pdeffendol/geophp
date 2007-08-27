<?php
require_once dirname(__FILE__).'/GeoPHP.php';
require_once dirname(__FILE__).'/EWKBUnpacker.php';

class EWKBParser
{
 	private $type_map = array(
		1 => 'point',
		2 => 'line_string',
		3 => 'polygon',
		4 => 'multi_point',
		5 => 'multi_line_string',
		6 => 'multi_polygon',
		7 => 'geometry_collection'
 		);
 	
 	private $srid;
 	private $with_z;
 	private $with_m;
 	
 	protected function parse($ewkb)
 	{
 		$this->unpacker = new EWKBUnpacker($ewkb);
 		$this->srid = null;
 		$this->with_z = null;
 		$this->with_m = null;
 		return $this->parse_geometry();
 	}	
 	
 	private function parse_geometry()
 	{
		$this->unpacker->set_endianness($this->unpacker->read_byte());
		$type = $this->unpacker->read_uint();
		
		if ($type & GeoPHP::Z_MASK)
		{
			$this->with_z = true;
			$type = ($type & ~GeoPHP::Z_MASK);
		}
		
		if ($type & GeoPHP::M_MASK)
		{
			$this->with_m = true;
			$type = ($type & ~GeoPHP::M_MASK);
		}
		
		if ($type & GeoPHP::SRID_MASK)
		{
			$this->srid = $this->unpacker->read_uint();
			$type = $type & ~GeoPHP::SRID_MASK;
		}
		elseif (!$this->srid)
		{
			// SRID is not present in parts of multi geometries, so use the parent
			$this->srid = GeoPHP::DEFAULT_SRID;
		}
		
		if ($this->type_map[$type])
		{
			$func = "parse_".$this->type_map[$type];
			return $this->$func();
		}
		else
		{
			throw new EWKBFormatError("Invalid geometry type");
		}
 	}
 	
 	private function parse_point()
 	{
 		$x = $this->unpacker->read_double();
 		$y = $this->unpacker->read_double();
 		if (!$this->with_z && !$this->with_m)
 		{
 			return GeoPHP_Point::from_xy($x, $y, $this->srid);
 		}
 		elseif ($this->with_z && $this->with_m)
 		{
 			$z = $this->unpacker->read_double();
 			$m = $this->unpacker->read_double();
 			return GeoPHP_Point::from_xyzm($x, $y, $z, $m, $this->srid);
 		}
 		elseif ($this->with_z)
 		{
 			$z = $this->unpacker->read_double();
 			return GeoPHP_Point::from_xyz($x, $y, $z, $this->srid);
 		}
 		else // with_m
 		{
 			$m = $this->unpacker->read_double();
 			return GeoPHP_Point::from_xym($x, $y, $m, $this->srid);
 		}
 	}
 	
 	private function parse_line_string()
 	{
 		return $this->parse_point_list('GeoPHP_LinearRing');
 	}
 	
 	private function parse_linear_ring()
 	{
 		return $this->parse_point_list('GeoPHP_LinearRing');
 	}
 	
 	private function parse_polygon()
 	{
 		$num_rings = $this->unpacker->read_uint();
 		$rings = array();
 		for ($i=0; $i<$num_rings; $i++)
 		{
 			$rings[] = $this->parse_linear_ring();
 		}
 		
 		return GeoPHP_Polygon::from_linear_rings($rings, $this->srid);
 	}
 	
 	private function parse_multi_point()
 	{
 		return $this->parse_multi_geometries('GeoPHP_MultiPoint');
 	}
 	
 	private function parse_multi_line_string()
 	{
 		return $this->parse_multi_geometries('GeoPHP_MultiLineString');
 	}
 	
 	private function parse_multi_polygon()
 	{
 		return $this->parse_multi_geometries('GeoPHP_MultiPolygon');
 	}
 	
 	private function parse_geometry_collection()
 	{
 		return $this->parse_multi_geometries('GeoPHP_GeometryCollection');
 	}

 	private function parse_multi_geometries($type)
 	{
 		$num_geometries = $this->unpacker->read_uint();
 		$geoms = array();
 		for ($i=0; $i<$num_geometries; $i++)
 		{
 			$geoms[] = $this->parse_geometry();
 		}
 		
 		return call_user_func(array($type, 'from_geometries'), $geoms, $this->srid);
 	}
 	
 	private function parse_point_list($type)
 	{
 		$num_points = $this->unpacker->read_uint();
 		$points = array();
 		for ($i=0; $i<$num_points; $i++)
 		{
 			$points[] = $this->parse_point();
 		}
 		
 		return call_user_func(array($type, 'from_points'), $points, $this->srid);
 	}
}
?>
