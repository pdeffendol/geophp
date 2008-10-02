<?php
require_once dirname(__FILE__).'/../GeoPHP.php';

abstract class GeoPHP_Geocoder
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
