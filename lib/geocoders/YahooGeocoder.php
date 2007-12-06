<?php
class GeoPHP_YahooGeocoder extends GeoPHP_Geocoder
{
	private $api_url = 'http://local.yahooapis.com/MapsService/V1/geocode';
	private $api_key = null;
	
	public function lookup($location)
	{
		$params = array(
			'appid' => $this->api_key,
			'location' => rawurlencode($location)
		);
		
		$url = $this->api_url.'?'.http_build_query($params);
		
		$result = file_get_contents($url);
	}
}
?>