<?php
namespace Modeler\Properties;

use Modeler\Exceptions\NotNullException;
use Modeler\Property;

class RelationProperty extends BaseProperty
{
    protected $relationClass;

    /**
     * RelationProperty constructor.
     * @param $relationClass
     */
    public function __construct($relationClass)
    {
        $this->relationClass = $relationClass;
        $this->setType(Property::RELATION);
    }

    /**
     * @param array $defaultValue
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
        return call_user_func([$this->relationClass, 'fromArray'], parent::prepareValue($value));
    }
}