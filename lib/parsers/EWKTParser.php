<?php
require_once dirname(__FILE__).'/../GeoPHP.php';
require_once dirname(__FILE__).'/EWKTTokenizer.php';

class GeoPHP_EWKTParser
{
 	private $type_map = array(
		'POINT' => 'point',
		'LINESTRING' => 'line_string',
		'POLYGON' => 'polygon',
		'MULTIPOINT' => 'multi_point',
		'MULTILINESTRING' => 'multi_line_string',
		'MULTIPOLYGON' => 'multi_polygon',
		'GEOMETRYCOLLECTION' => 'geometry_collection'
 		);
 	
 	private $tokenizer;
 	private $srid;
 	private $with_z;
 	private $with_m;
 	private $is_3dm;
 	
 	public function parse($ewkt)
 	{
		$this->tokenizer = new GeoPHP_EWKTTokenizer($ewkt);
 		$this->srid = null;
 		$this->with_z = false;
 		$this->with_m = false;
 		$this->is_3dm = false;
 		$geom = $this->parse_geometry(true);
 		$this->tokenizer->done();
 		return $geom;
 	}	
 	
 	private function parse_geometry($allow_srid)
 	{
		
		$token = $this->tokenizer->get_next_token();
		if ($token == "SRID")
		{
			// Parse SRID=nnnn
			if (!$allow_srid)
			{
				throw new GeoPHP_EWKTFormatError('SRID not allowed here');
			}
			if ($this->tokenizer->get_next_token() != '=')
			{
				throw new GeoPHP_EWKTFormatError('Invalid SRID expression');
			}
			
			$this->srid = intval($this->tokenizer->get_next_token());
			if ($this->tokenizer->get_next_token() != ';')
			{
				throw new GeoPHP_EWKTFormatError('Invalid SRID separator');
			}
			
			$type = $this->tokenizer->get_next_token();
		}
		else
		{
			$this->srid = $this->srid?$this->srid:GeoPHP::DEFAULT_SRID;
			$type = $token;
		}
		
		if (substr($type, -1, 1) == 'M')
		{
			$this->is_3dm = true;
			$this->with_m = true;
			$type = substr($type, 0, -1);
		}
		
		if ($this->type_map[$type])
		{
			$func = "parse_".$this->type_map[$type];
			return $this->$func();
		}
		else
		{
			throw new GeoPHP_EWKTFormatError("Invalid geometry type: ".$type);
		}
 	}
 	
 	private function parse_coords()
 	{
 		$x = $this->tokenizer->get_next_token();
 		$y = $this->tokenizer->get_next_token();
 		
 		if ($x === null || $y === null)
 		{
 			throw new GeoPHP_EWKTFormatError('Bad POINT format');
 		}
 		
 		if ($this->is_3dm)
 		{
 			$m = $this->tokenizer->get_next_token();
 			
 			if ($m === null || $m == ',' || $m == ')')
 			{
 				throw new GeoPHP_EWKTFormatError('m component expected but not found');
 			}
 			else
 			{
 				$point = GeoPHP_Point::from_xym(floatval($x), floatval($y), floatval($m), $this->srid);
 				$next = $this->tokenizer->get_next_token();
 			}
 		}
 		else
 		{
 			$z = $this->tokenizer->get_next_token();
 			
 			if ($z === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 			
 			if ($z == ',' || $z == ')')
 			{
 				// No Z value
 				$point = GeoPHP_Point::from_xy(floatval($x), floatval($y), $this->srid);
 				$next = $z;
 			}
 			else
 			{
	 			$m = $this->tokenizer->get_next_token();
	 			
	 			if ($m === null)
	 			{
	 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
	 			}
	 			
 				$this->with_z = true;
	 			if ($m == ',' || $m == ')')
	 			{
	 				// 3dz
	 				$point = GeoPHP_Point::from_xyz(floatval($x), floatval($y), floatval($z), $this->srid);
	 				$next = $m;
	 			}
	 			else
	 			{
	 				// 4d
	 				$this->with_m = true; 
	 				$point = GeoPHP_Point::from_xyzm(floatval($x), floatval($y), floatval($z), floatval($m), $this->srid);
	 				$next = $this->tokenizer->get_next_token();
	 			}
 			}
 		}
 		
 		return array($next, $point);
 	}
 	
 	private function parse_point()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid POINT');
 		}
 		
 		list($token, $point) = $this->parse_coords();
 		
 		if ($token != ')')
 		{
 			throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 		}
 		
 		return $point;
 	}
 	
 	private function parse_line_string()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid LINESTRING');
 		}

 		return $this->parse_point_list('GeoPHP_LineString');
 	}
 	
 	private function parse_linear_ring()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid Linear Ring');
 		}

 		return $this->parse_point_list('GeoPHP_LinearRing');
 	}
 	
 	private function parse_polygon()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid POLYGON');
 		}
 		
 		$token = '';
 		$rings = array();
 		while ($token != ')')
 		{
 			$rings[] = $this->parse_linear_ring();
 			$token = $this->tokenizer->get_next_token(); // comma
 			if ($token === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 		}
 		
 		return GeoPHP_Polygon::from_linear_rings($rings, $this->srid, $this->with_z, $this->with_m);
 	}
 	
	/**
	 * PostGIS doesn't put parens around each point.  The specification
	 * does.  Have to handle both cases here.
	 */
 	private function parse_multi_point()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid MULTIPOINT');
 		}

		$token = $this->tokenizer->check_next_token();
		if ($token == '(')
		{
			// Follow spec
	 		$token = '';
	 		$points = array();
	 		while ($token != ')')
	 		{
	 			$points[] = $this->parse_point();
	 			$token = $this->tokenizer->get_next_token(); // comma
	 			if ($token === null)
	 			{
	 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
	 			}
	 		}
	 		
	 		return GeoPHP_MultiPoint::from_points($points, $this->srid, $this->with_z, $this->with_m);
		}
		else
		{
			// PostGIS format
			return $this->parse_point_list('GeoPHP_MultiPoint');
		}
 	}
 	
 	private function parse_multi_line_string()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid MULTILINESTRING');
 		}
 		
 		$token = '';
 		$lines = array();
 		while ($token != ')')
 		{
 			$lines[] = $this->parse_line_string();
 			$token = $this->tokenizer->get_next_token(); // comma
 			if ($token === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 		}
 		
 		return GeoPHP_MultiLineString::from_line_strings($lines, $this->srid, $this->with_z, $this->with_m);
 	}
 	
 	private function parse_multi_polygon()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid MULTIPOLYGON');
 		}
 		
 		$token = '';
 		$polys = array();
 		while ($token != ')')
 		{
 			$polys[] = $this->parse_polygon();
 			$token = $this->tokenizer->get_next_token(); // comma
 			if ($token === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 		}
 		
 		return GeoPHP_MultiPolygon::from_polygons($polys, $this->srid, $this->with_z, $this->with_m);
 	}
 	
 	private function parse_geometry_collection()
 	{
 		if ($this->tokenizer->get_next_token() != '(')
 		{
 			throw new GeoPHP_EWKTFormatError('Invalid GEOMETRYCOLLECTION');
 		}
 		
 		$token = '';
 		$geoms = array();
 		while ($token != ')')
 		{
 			$geoms[] = $this->parse_geometry(false);
 			$token = $this->tokenizer->get_next_token(); // comma
 			if ($token === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 		}
 		
 		return GeoPHP_GeometryCollection::from_geometries($geoms, $this->srid, $this->with_z, $this->with_m);
 	}

 	private function parse_point_list($type)
 	{
 		$points = array();
 		
 		$token = '';
 		while ($token != ')')
 		{
 			list($token, $points[]) = $this->parse_coords();
 			if ($token === null)
 			{
 				throw new GeoPHP_EWKTFormatError('Incorrect termination of EWKT string');
 			}
 		}

 		return call_user_func(array($type, 'from_points'), $points, $this->srid, $this->with_z, $this->with_m);
 	}
}
?>
