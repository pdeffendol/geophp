<?php
namespace GeoPHP\Feature;

class MultiLineString extends GeometryCollection
{
    public function __construct($srid = null, $with_z = false, $with_m = false)
    {
        parent::__construct($srid, $with_z, $with_m);
        $this->binary_type = 5;
        $this->text_type = 'MULTILINESTRING';
    }

    public function __get($name)
    {
        switch ($name) {
            case 'lines':
                return $this->geometries;
                break;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'lines':
                $this->geometries = $value;
                break;
        }
    }

    public function text_representation($allow_z = true, $allow_m = true)
    {
        return implode(',', array_map(create_function('$line', 'return "(".$line->text_representation('.intval($allow_z).', '.intval($allow_m).').")";'), $this->lines));
    }

    public static function from_line_strings($lines, $srid = null, $with_z = false, $with_m = false)
    {
        return self::from_geometries($lines, $srid, $with_z, $with_m);
    }

    public static function from_geometries($geometries, $srid = null, $with_z = false, $with_m = false)
    {
        $coll = new self($srid, $with_z, $with_m);
        $coll->geometries = $geometries;

        return $coll;
    }

    public static function from_array($point_sets, $srid = null, $with_z = false, $with_m = false)
    {
        $ml = new self($srid, $with_z, $with_m);
        foreach ($point_sets as $set) {
            $ml->geometries[] = Linestring::from_array($set, $srid, $with_z, $with_m);
        }

        return $ml;
    }
}
