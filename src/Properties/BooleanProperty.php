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
        $value = parent::prepareValue($value);

        if (is_null($value)) {
            return null;
        }

        switch (strtolower($value)) {
            case 'true': return true;
            case 'false': return false;
        }

        return boolval($value);
    }
}