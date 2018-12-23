<?php
include_once __DIR__ . "/../examples/NestedModel.php";

/**
 * Class BaseModel
 *
 * @method boolean hasStringProperty()
 * @method string getStringProperty()
 * @method boolean hasIntegerProperty()
 * @method integer getIntegerProperty()
 * @method boolean hasFloatProperty()
 * @method float getFloatProperty()
 * @method boolean hasBooleanProperty()
 * @method boolean getBooleanProperty()
 * @method boolean hasRelationProperty()
 * @method NestedModel getRelationProperty()
 * @method boolean hasNotNullProperty()
 * @method string getNotNullProperty()
 * @method boolean hasWithDefaultValueProperty()
 * @method string getWithDefaultValueProperty()
 */
class BaseModel extends \Modeler\Model
{
    protected static function mapProperties()
    {
        return [
            'string_property' => \Modeler\Property::string(),
            'integer_property' => \Modeler\Property::integer(),
            'float_property' => \Modeler\Property::float(),
            'boolean_property' => \Modeler\Property::boolean(),
            'relation_property' => \Modeler\Property::relation(NestedModel::class),
            'relation_not_null_property' => \Modeler\Property::relation(NestedModel::class)->notNull(),
            'not_null_property' => \Modeler\Property::string()->notNull(),
            'with_default_value_property' => \Modeler\Property::string()->notNull()->defaultValue('default value'),
        ];
    }
}