<?php
namespace GeoPHP\Feature;

use GeoPHP\Constants;

/**
 * Represents the bounding box of a geometry.
 */
class Envelope
{
    /**
     * Lower left corner point
     * @var Point
     */
    public $ll;

    /**
     * Upper right corner point
     * @var Point
     */
    public $ur;

    public $srid;
    public $with_z;

    public function __construct($srid = null, $with_z = false)
    {
        $this->srid = $srid === null ? Constants::DEFAULT_SRID : $srid;
        $this->with_z = $with_z;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'top':
                return $this->ur->y;
            case 'right':
                return $this->ur->x;
            case 'bottom':
                return $this->ll->y;
            case 'left':
                return $this->ll->x;
        }
    }

    /**
     * Calculate the center point of the box
     */
    public function center()
    {
        return Point::from_xy(($this->ll->x + $this->ur->x)/2, ($this->ll->y + $this->ur->y)/2);
    }

    /**
     * Construct an Envelope from an array of two Point objects
     */
    public static function from_points($points, $srid = null, $with_z = false)
    {
        $e = new self($srid, $with_z);
        list($e->ll, $e->ur) = $points;

        return $e;
    }

    /**
     * Construct an Envelope from a set of coordinates in array format
     */
    public static function from_array($coords, $srid = null, $with_z = false)
    {
        $e = new self($srid, $with_z);
        $e->ll = Point::from_array($coords[0], $srid, $with_z);
        $e->ur = Point::from_array($coords[1], $srid, $with_z);

        return $e;
    }
}
