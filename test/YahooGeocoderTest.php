<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../lib/geocoders/YahooGeocoder.php';
require_once dirname(__FILE__).'/../lib/GeoPHP.php';

class YahooGeocoderTest extends PHPUnit_Framework_TestCase
{

	public function test01_locate()
	{
		// Test with no options and empty location string.
		$this->setExpectedException('GeoPhp_YahooGeocoderError');
		$coder = new GeoPHP_YahooGeocoder("djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--");
		$result = $coder->locate("");
	}

	public function test02_locate()
	{
		// Test with no options and good location string.
		$coder = new GeoPHP_YahooGeocoder("djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--");
		$result = $coder->locate("84321");
		$this->assertNotNull($result);
		$this->assertTrue(is_array($result));
		$this->assertEquals(1,count($result));
		$this->assertEquals(41.719610, $result[0]['coordinates']->x);
	}

	public function test03_locate()
	{
		// Test with no options and good location string.
		$coder = new GeoPHP_YahooGeocoder("djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--");
		$result = $coder->locate("logan");

		$this->assertNotNull($result);
		$this->assertTrue(count($result)>1);
	}

	public function test04_locate()
	{
		// Test with cache option provided and good location string.
		$flle_path = dirname(__FILE__)."/YahooCache";
		$coder = new GeoPHP_YahooGeocoder("djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--",array("cache" => $flle_path));

		$file_name = $flle_path."/".md5("76209");
		if (file_exists($file_name))
			unlink($file_name);

		$result = $coder->locate("76209");
		$this->assertTrue(file_exists($file_name));
		$this->assertNotNull($result);

		unlink($file_name);
		rmdir($flle_path);
	}

	public function test05_caching()
	{
		// Test with cache option provided and good location string.
		$flle_path = dirname(__FILE__)."/YahooCache";
		$coder = new GeoPHP_YahooGeocoder("djDozJ3V34HKPgWv.x_r0VhcywuZZdDAmWVcDafuhWb074C434xjhxAm_XNoXOGrGw--",array("cache" => $flle_path));

		$file_name = $flle_path."/".md5("84321");

		if (file_exists($file_name))
			unlink($file_name);

		$result1 = $coder->locate("84321");
		$this->assertTrue(file_exists($file_name));
		$this->assertNotNull($result1);

		$result2 = $coder->locate("84321");
		$this->assertEquals($result1,$result2);

		unlink($file_name);
		rmdir($flle_path);
	}
}
?>