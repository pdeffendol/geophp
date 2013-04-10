<?php
namespace GeoPHP\Feature;

class Polygon extends Geometry
{
    public $rings;

    public function __construct($srid = null, $with_z = false, $with_m = false)
    {
        parent::__construct($srid, $with_z, $with_m);
        $this->binary_type = 3;
        $this->text_type = 'POLYGON';
        $this->rings = array();
    }

    public function bounding_box()
    {
        if (!$this->with_z)
        {
            return $this->rings[0]->bounding_box();
        }
        else
        {
            $bbox = $this->rings[0]->bounding_box();
            $min_z = $bbox[0]->z;
            $max_z = $bbox[1]->z;

            for($i=1; $i<count($this->rings); $i++)
            {
                $ringbbox = $this->rings[$i]->bounding_box();
                $ll = $ringbbox[0];
                $ur = $ringbbox[1];
                if ($ur->z > $max_z) $max_z = $ur->z;
                if ($ll->z > $min_z) $min_z = $ll->z;

            }
            $bbox[0]->z = $min_z;
            $bbox[1]->z = $max_z;

            return $bbox;
        }
    }

    public function binary_representation($allow_z = true, $allow_m = true)
    {
        $rep = pack('V', count($this->rings));
        foreach ($this->rings as $ring)
        {
            $rep .= $ring->binary_representation($allow_z, $allow_m);
        }
        return $rep;
    }

    public function text_representation($allow_z = true, $allow_m = true)
    {
        return implode(',', array_map(create_function('$ring', 'return "(".$ring->text_representation('.intval($allow_z).', '.intval($allow_m).').")";'), $this->rings));
    }

    public static function from_linear_rings($rings, $srid = null, $with_z = false, $with_m = false)
    {
        $poly = new self($srid, $with_z, $with_m);
        $poly->rings = $rings;
        return $poly;
    }

    public static function from_array($point_sets, $srid = null, $with_z = false, $with_m = false)
    {
        $poly = new self($srid, $with_z, $with_m);
        foreach ($point_sets as $set)
        {
            $poly->rings[] = LinearRing::from_array($set, $srid, $with_z, $with_m);
        }
        return $poly;
    }
}
?>
