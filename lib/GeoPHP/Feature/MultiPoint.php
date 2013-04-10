<?php
namespace GeoPHP\Feature;

class MultiPoint extends GeometryCollection
{
    public function __construct($srid = null, $with_z = false, $with_m = false)
    {
        parent::__construct($srid, $with_z, $with_m);
        $this->binary_type = 4;
        $this->text_type = 'MULTIPOINT';
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'points':
                return $this->geometries;
                break;
        }
    }

    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'points':
                $this->geometries = $value;
                break;
        }
    }

    public function text_representation($allow_z = true, $allow_m = true)
    {
        return implode(',', array_map(create_function('$p', 'return $p->text_representation('.intval($allow_z).','.intval($allow_m).');'), $this->geometries));
    }

    public static function from_points($points, $srid = null, $with_z = false, $with_m = false)
    {
        return self::from_geometries($points, $srid, $with_z, $with_m);
    }

    public static function from_geometries($geometries, $srid = null, $with_z = false, $with_m = false)
    {
        $coll = new self($srid, $with_z, $with_m);
        $coll->geometries = $geometries;
        return $coll;
    }

    public static function from_array($points, $srid = null, $with_z = false, $with_m = false)
    {
        $mp = new self($srid, $with_z, $with_m);
        foreach ($points as $point)
        {
            $mp->geometries[] = Point::from_array($point, $srid, $with_z, $with_m);
        }
        return $mp;
    }
}
