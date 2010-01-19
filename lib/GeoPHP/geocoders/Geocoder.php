<?php
namespace GeoPHP;

abstract class Geocoder
{
	protected $options;
	function __construct($options = null)
	{
		$this->options = array(
			"cache" => null,
			"cache_timeout" => 7200
		);

		if ($options !== null)
			$this->options = array_merge($this->options, $options);
	}

	public function locate($location)
	{

	}
}
?>
