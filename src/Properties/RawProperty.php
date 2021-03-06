<?php
namespace Modeler\Properties;

use Modeler\Exceptions\NotNullException;
use Modeler\Property;

class RawProperty extends BaseProperty
{
    public function __construct()
    {
        $this->setType(Property::BOOLEAN);
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

        return $value;
    }
}