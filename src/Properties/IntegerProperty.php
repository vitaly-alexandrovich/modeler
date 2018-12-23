<?php
namespace Modeler\Properties;

use Modeler\Exceptions\NotNullException;
use Modeler\Property;

class IntegerProperty extends BaseProperty
{
    public function __construct()
    {
        $this->setType(Property::INTEGER);
    }

    /**
     * @param integer $defaultValue
     * @return static
     */
    public function defaultValue($defaultValue)
    {
        parent::defaultValue($defaultValue);
        return $this;
    }

    /**
     * @param $value
     * @return integer|null
     * @throws NotNullException
     */
    public function prepareValue($value)
    {
        $value = parent::prepareValue($value);

        if (is_null($value)) {
            return null;
        }

        return intval($value);
    }
}