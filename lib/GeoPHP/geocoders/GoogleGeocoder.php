<?php
namespace GeoPHP;

class GoogleGeocoder extends Geocoder
{
	private $api_url = 'http://maps.google.com/maps/geo';
//	private $api_key = "ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ";
	private $api_key ="";
	private $is_caching = false;
	private $cache_file_path;
	private $cache_filename;

	function __construct($my_key, $options = null)
	{
		parent::__construct($options);

		$this->api_key = $my_key;

		if ($this->options['cache'] !== null)
		{
			$this->is_caching = true;
			$this->cache_file_path = $this->options['cache'];

			if (!file_exists($this->cache_file_path))
			{
				mkdir($this->cache_file_path,0777);
			}
		}
		else
			$this->is_caching = false;
	}

	public function locate($location)
	{
		$params = array(
			'q' => $location,
			'key' => $this->api_key,
			'output' => "json"
		);

		$request_url = $this->api_url.'?'.http_build_query($params);
       
		// Check the cache
		if ($this->is_caching && $this->cache_exists($location))
			$response = $this->get_file_data($this->cache_filename);
		else
		{
			$response = $this->get_url_response($request_url);
			if ($this->is_caching)
				$this->cache_response($response,$this->cache_filename);
		}

		return $this->format_output($response);
	}

	/*
	 * This function format the output and returns an array
	 */
	private function format_output($response)
	{
		$location_details = array();
		$res = json_decode($response,true);
		$results = $res['Placemark'];

		foreach ($results as $res)
		{
			$result = array_change_key_case($res,CASE_LOWER);
			$p = Point::from_xy($result['point']['coordinates'][1],$result['point']['coordinates'][0]);
			$output = array();

			$output['coordinates'] = $p;
			$output['address'] = $result['address'];
			if (isset($result['addressdetails']['Country']['AdministrativeArea']['Locality']))
				$output['city'] = $result['addressdetails']['Country']['AdministrativeArea']['Locality']['LocalityName'];
			else
				$output['city'] = $result['addressdetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['SubAdministrativeAreaName'];

			$output['state'] = $result['addressdetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
			if (isset($result['addressdetails']['Country']['AdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber']))
			{
				$output['zip'] = $result['addressdetails']['Country']['AdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber'];
			}
			$output['country'] = $result['addressdetails']['Country']['CountryNameCode'];
			$location_details[] = $output;
		}
		return $location_details;
	}


	/*
	 * This function checks if the cache file exists.
	 */
	private function cache_exists($location)
	{
		$this->cache_filename = $this->cache_file_path."/".$this->generate_cache_filename($location);
		if (file_exists($this->cache_filename) && !(filemtime($this->cache_filename) < ($_SERVER['REQUEST_TIME'] -$this->options['cache_timeout'])))
			return true;
		else
			return false;
	}

	/*
	 * 	 This function generate the cache filename.
	 */
	 private function generate_cache_filename($location)
	 {
	 	return md5($location);
	 }

	/*
	 * Save the Url response into a cache File.
	 */
	private function cache_response($response, $cache_filename)
	{
		if ($response !== false)
		{
			$tmpf = tempnam('/tmp','GOOGLE');
			$fp = fopen($tmpf,"w");
			fwrite($fp, $response);
			fclose($fp);
			rename($tmpf, $cache_filename);
		}
	}

	/*
	 *	This function retrieve the data from the file.
	 */

	 private function get_file_data($filename)
	 {
		return file_get_contents($filename);
	 }

	 /*
	 *	This function get the response from the url
	 */

	 private function get_url_response($url)
	 {
		$response = file_get_contents($url);
		$res = json_decode($response,true);
		try
		{
			if ($res['Status']['code'] == 200)
				return $response;
			else
			{
				throw new GoogleGeocoderError("Bad Request made.");
			}	
		}	
		catch (GeoPhp_GoogleGeocoderError $geo)
		{
			return $geo->getMessage();
		}	
	 }
}
?>