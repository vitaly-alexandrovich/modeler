<?php
namespace Modeler\Properties;

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
        return intval(parent::prepareValue($value));
    }
}