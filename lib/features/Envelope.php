<?php
class GeoPHP_Envelope
{
	private $ll;
	private $ur;
	public $srid;
	public $with_z;
	
	public function __construct($srid = null, $with_z = false)
	{
		$this->srid = $srid===null?GeoPHP::DEFAULT_SRID:$srid;
		$this->with_z = $with_z;
	}
	
	public static function from_points($ll, $ur, $srid = null, $with_z = false)
	{
		
	}
	
	public function center()
	{
		return GeoPHP_Point::from_xy(($this->ll->x + $this->ur->x)/2, ($this->ll->y + $this->ur->y)/2);
	}
}
?>
