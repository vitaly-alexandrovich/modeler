<?php
namespace Modeler\Properties;

class FloatProperty extends BaseProperty
{
    public function __construct()
    {
        $this->setType(Property::FLOAT);
    }

    /**
     * @param float $defaultValue
     * @return static
     */
    public function defaultValue($defaultValue)
    {
        parent::defaultValue($defaultValue);
        return $this;
    }

    /**
     * @param $value
     * @return float|null
     * @throws NotNullException
     */
    public function prepareValue($value)
    {
        return floatval(parent::prepareValue($value));
    }
}