<?php
namespace GeoPHP\Feature;

use GeoPHP\Constants;

abstract class Geometry
{
	/**
	 * SRID of the data
	 */
	public $srid;
	
	/**
	 * Whether z component of geometry is meaningful
	 */
	public $with_z;
	
	/**
	 * Whether m component of geometry is meaningful
	 */
	public $with_m;
	
	/**
	 * WKB type code of this geometry type
	 */
	public $binary_type;
	
	/**
	 * WKT type string of this geometry type
	 */
	public $text_type;
	
	public function __construct($srid = null, $with_z = false, $with_m = false)
	{
		$this->srid = $srid === null ? Constants::DEFAULT_SRID : $srid;
		$this->with_z = $with_z;
		$this->with_m = $with_m;
	}
	
	/**
	 * Calculate the bounds of the geometry as a set of two Points.
	 */
	public function bounding_box()
	{
		// Implement in child classes
	}
	
	/**
	 * Calculate the extent of the geometry as an Envelope object
	 */
	public function extent()
	{
		return Envelope::from_points($this->bounding_box(), $this->srid, $this->with_z);		
	}
	
	public function envelope()
	{
		return $this->extent();
	}
	
	public function to_ewkb($allow_srid = true, $allow_z = true, $allow_m = true)
	{
		$ewkb = '';
		
		$ewkb .= chr(1); // little endian
		
		$type = $this->binary_type;
		if ($this->with_z && $allow_z)
		{
			$type |= Constants::Z_MASK;
		}
		if ($this->with_m && $allow_m)
		{
			$type |= Constants::M_MASK;
		}
		if ($this->srid != Constants::DEFAULT_SRID && $allow_srid)
		{
			$type |= Constants::SRID_MASK;
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
		if ($this->srid != Constants::DEFAULT_SRID && $allow_srid)
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
	
	public static function from_ewkt($ewkt)
	{
		$parser = new EWKTParser;
		return $parser->parse($ewkt);
	}
	
	public static function from_ewkb($ewkb)
	{
		$parser = new EWKBParser;
		return $parser->parse($ewkb);
	}
	
	public static function from_hexewkb($hexewkb)
	{
		$parser = new HexEWKBParser;
		return $parser->parse($hexewkb);
	}
}
?>
