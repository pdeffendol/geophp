<?php
require_once dirname(__FILE__).'/EWKBParser.php';

class GeoPHP_HexEWKBParser extends GeoPHP_EWKBParser
{
	public function decode_hex($hex)
	{
		$result = '';
		$bytes = intval((strlen($hex) + 1) / 2);
		for ($i=0; $i<$bytes; $i++)
		{
			$result .= chr(hexdec(substr($hex, $i*2, 2)));
		}
		return $result;
	}
	
	public function parse($hexewkb)
	{
		return parent::parse($this->decode_hex($hexewkb));
	}
}
?>