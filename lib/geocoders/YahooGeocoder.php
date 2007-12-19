<?php
require_once dirname(__FILE__).'/Geocoder.php';
require_once dirname(__FILE__).'/YahooGeocoderError.php';
require_once dirname(__FILE__).'/../CCurl.php';

class GeoPHP_YahooGeocoder extends GeoPHP_Geocoder
{
	private $api_url = 'http://local.yahooapis.com/MapsService/V1/geocode';
//	private $api_key = "djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--";
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
			'appid' => $this->api_key,
			'location' => rawurlencode($location),
			'output' => "php"
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
		include_once dirname(__FILE__).'/../features/Point.php';
		$r = unserialize($response);

		$location_details = array();
		$results = array();

		if (array_key_exists('Latitude',$r['ResultSet']['Result']))
			$results[]=$r['ResultSet']['Result'];
		else
			$results = $r['ResultSet']['Result'];

		foreach ($results as $res)
		{
			$result = array_change_key_case($res,CASE_LOWER);
			$p = new GeoPHP_Point();
			$p->set_xy($result['latitude'],$result['longitude']);

			$output = array();
			$output['precision'] = $result['precision'];
			$output['coordiantes'] = $p;
			$output['address'] = $result['address'];
			$output['city'] = $result['city'];
			$output['state'] = $result['state'];
			$output['zip'] = $result['zip'];
			$output['country'] = $result['country'];
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
			$tmpf = tempnam('/tmp','YWS');
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
		$curl = new CCurl($url);

		$response = $curl->execute();
		if (($status_code = $curl->get_status_code()) == 200)
			return $response;
		else
			throw new GeoPhp_YahooGeocoderError("Bad Request Status Code:: ".$status_code);

		$curl->close();
	 }
}
?>