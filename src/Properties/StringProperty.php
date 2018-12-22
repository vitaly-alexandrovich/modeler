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
        return strval(parent::prepareValue($value));
    }
}