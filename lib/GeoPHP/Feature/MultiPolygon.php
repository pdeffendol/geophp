<?php
namespace GeoPHP\Feature;

class MultiPolygon extends GeometryCollection
{
    public function __construct($srid = null, $with_z = false, $with_m = false)
    {
        parent::__construct($srid, $with_z, $with_m);
        $this->binary_type = 6;
        $this->text_type = 'MULTIPOLYGON';
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'polygons':
                return $this->geometries;
                break;
        }
    }
    
    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'polygons':
                $this->geometries = $value;
                break;
        }
    }
    
    public function text_representation($allow_z = true, $allow_m = true)
    {
        return implode(',', array_map(create_function('$poly', 'return "(".$poly->text_representation('.intval($allow_z).', '.intval($allow_m).').")";'), $this->polygons));
    }

    public static function from_polygons($polys, $srid = null, $with_z = false, $with_m = false)
    {
        return self::from_geometries($polys, $srid, $with_z, $with_m);
    }

    public static function from_geometries($geometries, $srid = null, $with_z = false, $with_m = false)
    {
        $coll = new self($srid, $with_z, $with_m);
        $coll->geometries = $geometries;
        return $coll;
    }

    public static function from_array($point_set_sets, $srid = null, $with_z = false, $with_m = false)
    {
        $mp = new self($srid, $with_z, $with_m);
        foreach ($point_set_sets as $point_set)
        {
            $mp->geometries[] = Polygon::from_array($point_set, $srid, $with_z, $with_m);
        }
        return $mp;
    }
}
?>