<?php
namespace Modeler;

use Modeler\Properties\BooleanProperty;
use Modeler\Properties\FloatProperty;
use Modeler\Properties\IntegerProperty;
use Modeler\Properties\RelationProperty;
use Modeler\Properties\StringProperty;

class Property
{
    const STRING = 'string';
    const INTEGER = 'integer';
    const FLOAT = 'float';
    const BOOLEAN = 'boolean';
    const RELATION = 'relation';

    /**
     * @return StringProperty
     */
    public static function string()
    {
        return new StringProperty();
    }

    /**
     * @return IntegerProperty
     */
    public static function integer()
    {
        return new IntegerProperty();
    }

    /**
     * @return FloatProperty
     */
    public static function float()
    {
        return new FloatProperty();
    }

    /**
     * @return BooleanProperty
     */
    public static function boolean()
    {
        return new BooleanProperty();
    }

    /**
     * @param string $relationClass
     * @return RelationProperty
     */
    public static function relation($relationClass)
    {
        return new RelationProperty($relationClass);
    }
}