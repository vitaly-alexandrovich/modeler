<?php
namespace Modeler\Properties;

use Modeler\Property;
use Modeler\Exceptions\NotNullException;

class StringProperty extends BaseProperty
{
    public function __construct()
    {
        $this->setType(Property::STRING);
    }

    /**
     * @param string $defaultValue
     * @return static
     */
    public function defaultValue($defaultValue)
    {
        parent::defaultValue($defaultValue);
        return $this;
    }

    /**
     * @param $value
     * @return string|null
     * @throws NotNullException
     */
    public function prepareValue($value)
    {
        $value = parent::prepareValue($value);

        if (is_null($value)) {
            return null;
        }

        return strval($value);
    }
}