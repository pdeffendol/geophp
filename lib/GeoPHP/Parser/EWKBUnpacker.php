<?php
namespace GeoPHP\Parser;

class EWKBUnpacker
{
	const NDR = 1;
	const XDR = 0;
	
	private $ewkb;
	private $position;
	
	private $uint_marker;
	private $double_marker;
	
	public function __construct($ewkb)
	{
		$this->ewkb = $ewkb;
		$this->position = 0;				
	}
	
	public function read_double()
	{
        $i = $this->position;
        $this->position += 8;
        $packed_double = substr($this->ewkb, $i, 8);
        if (!$packed_double || strlen($packed_double) < 8)
        {
        	throw new EWKBFormatError("Truncated data");
        }
        
        return current(unpack($this->double_marker."value", $packed_double));
	}
	
	public function read_uint()
	{
        $i = $this->position;
        $this->position += 4;
        $packed_uint = substr($this->ewkb, $i, 4);
        if (!$packed_uint || strlen($packed_uint) < 4)
        {
        	throw new EWKBFormatError("Truncated data");
        }
        
        return current(unpack($this->uint_marker."value", $packed_uint));
	}
	
	public function read_byte()
	{
        $i = $this->position;
        $this->position += 1;
        $packed_byte = substr($this->ewkb, $i, 1);
        if ($packed_byte === null || strlen($packed_byte) < 1)
        {
        	throw new EWKBFormatError("Truncated data");
        }
        
        return current(unpack("Cvalue", $packed_byte));
	}
	
	public function set_endianness($eness)
	{
		if ($eness == self::NDR)
		{
			$this->uint_marker = 'V';
			$this->double_marker = 'd'; // should be E
		}	
		elseif ($eness == self::XDR)
		{
			$this->uint_marker = 'N';
			$this->double_marker = 'd'; // should be G
		}	
	}
	
	public function done()
	{
		if ($this->position != strlen($this->ewkb))
		{
			throw new EWKBFormatError('Trailing data (read '.$this->position.' bytes, have '.strlen($this->ewkb).')');
		}
	}
}
?>
