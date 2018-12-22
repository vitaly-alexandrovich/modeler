<?php
namespace Modeler\Properties;

use Modeler\Exceptions\NotNullException;
use Modeler\Property;

class BooleanProperty extends BaseProperty
{
    public function __construct()
    {
        $this->setType(Property::BOOLEAN);
    }

    /**
     * @param boolean $defaultValue
     * @return static
     */
    public function defaultValue($defaultValue)
    {
        parent::defaultValue($defaultValue);
        return $this;
    }

    /**
     * @param $value
     * @return boolean|null
     * @throws NotNullException
     */
    public function prepareValue($value)
    {
        return boolval(parent::prepareValue($value));
    }
}