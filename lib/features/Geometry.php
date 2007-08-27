<?php
abstract class GeoPHP_Geometry
{
	public $srid;
	public $with_z;
	public $with_m;
	public $binary_type;
	public $text_type;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		$this->srid = $srid===null?GeoPHP::DEFAULT_SRID:$srid;
		$this->with_z = $with_z;
		$this->with_m = $with_m;
	}
	
	public function to_ewkb($allow_srid = true, $allow_z = true, $allow_m = true)
	{
		$ewkb = '';
		
		$ewkb .= chr(1); // little endian
		
		$type = $this->binary_type;
		if ($this->with_z && $allow_z)
		{
			$type |= GeoPHP::Z_MASK;
		}
		if ($this->with_m && $allow_m)
		{
			$type |= GeoPHP::M_MASK;
		}
		if ($this->srid != GeoPHP::DEFAULT_SRID && $allow_srid)
		{
			$type |= GeoPHP::SRID_MASK;
			$ewkb .= pack('VV', $type, $this->srid);
		}
		else
		{
			$ewkb .= pack('V', $type);
		}
		
		$ewkb .= $this->binary_representation($allow_z, $allow_m);
		return $ewkb;
	}
	
	public function to_wkb()
	{
		return $this->to_ewkb(false, false, false);
	}
	
	public function to_hexewkb($allow_srid = true, $allow_z = true, $allow_m = true)
	{
		$ewkb = $this->to_ewkb($allow_srid, $allow_z, $allow_m);
		$hex = '';
		for ($i = 0; $i < strlen($ewkb); $i++)
		{
			$hex .= strtoupper(sprintf('%02x', ord(substr($ewkb, $i, 1))));
		}
		return $hex;
	}
	
	public function to_ewkt($allow_srid = true, $allow_z = true, $allow_m = true)
	{
		$ewkt = '';
		if ($this->srid != GeoPHP::DEFAULT_SRID && $allow_srid)
		{
			$ewkt = 'SRID='.$this->srid.';';
		}			
		
		$ewkt .= $this->text_type;

		if ($this->with_m && $allow_m && (!$this->with_z || !$allow_z))
		{
			$ewkt .= 'M';
		}
		
		$ewkt .= '('.$this->text_representation($allow_z, $allow_m).')';
		return $ewkt;
	}
	
	public function to_wkt()
	{
		return $this->to_ewkt(false, false, false);
	}
}
?>
