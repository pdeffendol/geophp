<?php
namespace GeoPHP\Tests\Geocoder;

use GeoPHP\Geocoder\GoogleGeocoder;
use GeoPHP\Geocoder\GoogleGeocoderError;
use GeoPHP\Feature\Point;

class GoogleGeocoderTest extends \PHPUnit_Framework_TestCase
{

	public function test01_locate()
	{
		// Test with no options and empty location string.
		$this->setExpectedException('GeoPHP\Geocoder\GoogleGeocoderError');
		$coder = new GoogleGeocoder("ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ");
		$result = $coder->locate("");
	}

	public function test02_locate()
	{
		// Test with no options and good location string.
		$coder = new GoogleGeocoder("ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ");
		$result = $coder->locate("84321");
		$this->assertNotNull($result);
		$this->assertTrue(is_array($result));
		$this->assertEquals(1,count($result));
		$this->assertEquals(41.7131433, $result[0]['coordinates']->x);
	}

	public function test03_locate()
	{
		// Test with no options and good location string.
		$coder = new GoogleGeocoder("ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ");
		$result = $coder->locate("logan");

		$this->assertNotNull($result);
		$this->assertTrue(count($result) == 1);
	}

	public function test04_locate()
	{
		// Test with cache option provided and good location string.
		$flle_path = dirname(__FILE__)."/GoogleCache";
		$coder = new GoogleGeocoder("ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ",array("cache" => $flle_path));

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
		$flle_path = dirname(__FILE__)."/GoogleCache";
		$coder = new GoogleGeocoder("ABQIAAAA1WyaXpV_0Jhp1knJV5i7XRST-KG1lUPPqvb7mBlZbBX29iXO4hRN4syFNonIVSGmte-rI23N8vijwQ",array("cache" => $flle_path));

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