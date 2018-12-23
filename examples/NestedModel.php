<?php

/**
 * Class BaseModel
 *
 * @method boolean hasFoo()
 * @method string getFoo()
 * @method boolean hasBar()
 * @method string getBar()
 */
class NestedModel extends \Modeler\Model
{
    protected static function mapProperties()
    {
        return [
            'foo' => \Modeler\Property::string(),
            'bar' => \Modeler\Property::string()->defaultValue('zyx'),
            'hidden' => \Modeler\Property::string()->notNull(),
        ];
    }
}