<?php
namespace GeoPHP;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// Tests
require_once dirname(__FILE__).'/FeaturesTest.php';
require_once dirname(__FILE__).'/EWKBParserTest.php';
require_once dirname(__FILE__).'/EWKTParserTest.php';

class AllTests
{
	public static function main()
	{
		\PHPUnit_TextUI_TestRunner::run(self::suite());
	}
 
	public static function suite()
	{
		$suite = new \PHPUnit_Framework_TestSuite('GeoPHP Library');

		$suite->addTestSuite('GeoPHP\\FeaturesTest');
		$suite->addTestSuite('GeoPHP\\EWKBParserTest');
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
?>