<?php
namespace GeoPHP;

class Point extends Geometry
{
	public $x;
	public $y;
	public $z;
	public $m;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		parent::__construct($srid, $with_z, $with_m);
		$this->binary_type = 1;
		$this->text_type = 'POINT';
		
		$this->x = 0;
		$this->y = 0;
		$this->z = 0;
		$this->m = 0;
	}
	
	public function __get($name)
	{
		switch ($name)
		{
			case 'lat':
				return $this->y;
				break;
			case 'lon':
			case 'lng':
				return $this->x;
				break;
		}
	}
	
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'lat':
				$this->y = $value;
				break;
			case 'lon':
			case 'lng':
				$this->x = $value;
				break;
		}
	}
	
	public function set_xy($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}
	
	public function set_xyz($x, $y, $z)
	{
		$this->set_xy($x, $y);
		$this->z = $z;
	}
	
	public function bounding_box()
	{
		if (!$this->with_z)
		{
			return array(
				Point::from_xy($this->x, $this->y),
				Point::from_xy($this->x, $this->y)
			);
		}
		else
		{
			return array(
				Point::from_xyz($this->x, $this->y, $this->z),
				Point::from_xyz($this->x, $this->y, $this->z)
			);
		}
	}
	
	public function binary_representation($allow_z = true, $allow_m = true)
	{
        $rep = pack('dd', $this->x, $this->y);
        if ($this->with_z && $allow_z) $rep .= pack('d', $this->z);
        if ($this->with_m && $allow_m) $rep .= pack('d', $this->m);
        return $rep;
	}
	
	public function text_representation($allow_z = true, $allow_m = true)
	{
		$rep = $this->x.' '.$this->y;
		if ($this->with_z && $allow_z) $rep .= ' '.$this->z;
		if ($this->with_m && $allow_m) $rep .= ' '.$this->m;
		return $rep;
	}
	
	public static function from_xy($x, $y, $srid = null)
	{
		$point = new Point($srid);
		$point->set_xy($x, $y);
		return $point;
	}
	
	public static function from_xyz($x, $y, $z, $srid = null)
	{
		$point = new Point($srid, true);
		$point->set_xyz($x, $y, $z);
		return $point;
	}
	
	public static function from_lon_lat($x, $y, $srid = null)
	{
		return self::from_xy($x, $y, $srid);
	}
	
	public static function from_xym($x, $y, $m, $srid = null)
	{
		$point = new Point($srid, false, true);
		$point->set_xy($x, $y);
		$point->m = $m;
		return $point;
	}
	
	public static function from_xyzm($x, $y, $z, $m, $srid = null)
	{
		$point = new Point($srid, true, true);
		$point->set_xyz($x, $y, $z);
		$point->m = $m;
		return $point;
	}
	
	public static function from_array($coords, $srid = null, $with_z = false, $with_m = false)
	{
		if (!$with_z && !$with_m)
		{
			return self::from_xy($coords[0], $coords[1], $srid);
		}
		elseif ($with_z && $with_m)
		{
			return self::from_xyzm($coords[0], $coords[1], $coords[2], $coords[3], $srid);
		}
		elseif ($with_z)
		{
			return self::from_xyz($coords[0], $coords[1], $coords[2], $srid);
		}
		else
		{
			return self::from_xym($coords[0], $coords[1], $coords[2], $srid);
		}
	}
}
?>
