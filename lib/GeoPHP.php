<?php
require_once dirname(__FILE__).'/features/Envelope.php';
require_once dirname(__FILE__).'/features/Point.php';
require_once dirname(__FILE__).'/features/LineString.php';
require_once dirname(__FILE__).'/features/LinearRing.php';
require_once dirname(__FILE__).'/features/Polygon.php';
require_once dirname(__FILE__).'/features/GeometryCollection.php';
require_once dirname(__FILE__).'/features/MultiPoint.php';
require_once dirname(__FILE__).'/features/MultiLineString.php';
require_once dirname(__FILE__).'/features/MultiPolygon.php';
require_once dirname(__FILE__).'/EWKBParser.php';
require_once dirname(__FILE__).'/HexEWKBParser.php';

abstract class GeoPHP
{
	const Z_MASK = 0x80000000;
	const M_MASK = 0x40000000;
	const SRID_MASK = 0x20000000;

	const DEFAULT_SRID = -1;
}
?>
