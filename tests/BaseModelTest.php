<?php

use Modeler\Model;

include_once __DIR__ . "/../vendor/autoload.php";
include_once __DIR__ . "/../examples/BaseModel.php";

class BaseModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws Exception
     */
    public function testCanBeCreatedModel()
    {
        $model = BaseModel::fromArray([]);
        $this->assertInstanceOf(BaseModel::class, $model);
    }

    /**
     * @param $defaultModelData
     * @throws Exception
     *
     * @dataProvider providerBaseModelDefaultData
     */
    public function testCreateModelWithoutData($defaultModelData)
    {
        $model = BaseModel::fromArray([]);
        $this->assertEquals($model->toArray(), $defaultModelData);
    }

    /**
     * @param $data
     * @param $value
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testFromArray($data, $value)
    {
        $model = NestedModel::fromArray($data);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model), $value);
    }

    /**
     * @param $data
     * @param $value
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testFromJson($data, $value)
    {
        $model = NestedModel::fromJson(json_encode($data));

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model), $value);
    }

    /**
     * @param $data
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testToArray($data)
    {
        $model = new Model();

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($model, $data);

        $this->assertEquals($model->toArray(), $data);
    }

    /**
     * @param $data
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testGetAttribute($data)
    {
        $model = new Model();

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($model, $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($model->getAttribute($key), $value);
        }
    }

    /**
     * @param $data
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testSetAttribute($data)
    {
        $model = new Model();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        foreach ($data as $key => $value) {
            $this->assertEquals($attributesProperty->getValue($model)[$key], $value);
        }
    }

    /**
     * @param $data
     * @throws Exception
     * @dataProvider providerTestData
     */
    public function testToJson($data)
    {
        $model = new Model();

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($model, $data);

        $this->assertEquals($model->toJson(), json_encode($data));
    }

    /**
     * @param $value
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerNullableStringField
     */
    public function testNullableStringField($value, $result)
    {
        $model = BaseModel::fromArray(['string_property' => $value]);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model)['string_property'], $result);
    }

    /**
     * @param $data
     * @param $attribute
     * @param $result
     * @throws Exception
     * @dataProvider providerHasAttribute
     */
    public function testHasAttribute($data, $attribute, $result)
    {
        $model = NestedModel::fromArray($data);
        $this->assertEquals($model->hasAttribute($attribute), $result);
    }

    /**
     * @param $value
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerNullableIntegerField
     */
    public function testNullableIntegerField($value, $result)
    {
        $model = BaseModel::fromArray(['integer_property' => $value]);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model)['integer_property'], $result);
    }

    /**
     * @param $value
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerNullableFloatField
     */
    public function testNullableFloatField($value, $result)
    {
        $model = BaseModel::fromArray(['float_property' => $value]);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model)['float_property'], $result);
    }

    /**
     * @param $value
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerNullableBooleanField
     */
    public function testNullableBooleanField($value, $result)
    {
        $model = BaseModel::fromArray(['boolean_property' => $value]);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model)['boolean_property'], $result);
    }

    /**
     * @param $value
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerNullableRelationField
     */
    public function testNullableRelationField($value, $result)
    {
        $model = BaseModel::fromArray(['relation_property' => $value]);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        /** @var NestedModel $relation */
        $relation = $attributesProperty->getValue($model)['relation_property'];

        $this->assertInstanceOf(NestedModel::class, $relation);
        $this->assertEquals($relation->toArray(), $result);
    }

    /**
     * @param $attributesModel
     * @param $existsProperty
     * @throws ReflectionException
     * @throws Exception
     * @dataProvider providerNotNullField
     */
    public function testNotNullField($attributesModel, $existsProperty)
    {
        $model = BaseModel::fromArray($attributesModel);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals(isset($attributesProperty->getValue($model)['not_null_property']), $existsProperty);
    }

    /**
     * @param $attributesModel
     * @param $value
     * @throws ReflectionException
     * @throws Exception
     * @dataProvider providerDefaultValueField
     */
    public function testDefaultValueField($attributesModel, $value)
    {
        $model = BaseModel::fromArray($attributesModel);

        $attributesProperty = new ReflectionProperty($model, 'attributes');
        $attributesProperty->setAccessible(true);

        $this->assertEquals($attributesProperty->getValue($model)['with_default_value_property'], $value);
    }

    /**
     * @return array
     */
    public function providerDefaultValueField()
    {
        $defaultValue = $this->providerBaseModelDefaultData()[0][0]['with_default_value_property'];

        return [
            [[], $defaultValue],
            [['with_default_value_property' => null], $defaultValue],
            [['with_default_value_property' => 'test'], 'test'],
        ];
    }

    /**
     * @return array
     */
    public function providerNotNullField()
    {
        return [
            [[], false],
            [['not_null_property' => null], false],
            [['not_null_property' => 'test'], true],
        ];
    }

    /**
     * @return array
     */
    public function providerNullableRelationField()
    {
        return [
            [['foo' => '1', 'bar' => '2'], ['foo' => '1', 'bar' => '2']],
            [null, $this->providerBaseModelDefaultData()[0][0]['relation_property']],
        ];
    }

    /**
     * @return array
     */
    public function providerNullableBooleanField()
    {
        return [
            [false, false],
            ['', false],
            ['false', false],
            ['true', true],
            ['0', false],
            ['1', true],
            [0, false],
            [1, true],
            [null, null]
        ];
    }

    /**
     * @return array
     */
    public function providerNullableFloatField()
    {
        return [
            [123.5, 123.5],
            [120, 120.0],
            ['130', 130.0],
            ['130.9', 130.9],
            ['test', 0.0],
            [true, 1.0],
            [false, 0.0],
            [null, null]
        ];
    }

    /**
     * @return array
     */
    public function providerNullableIntegerField()
    {
        return [
            [123, 123],
            ['test', 0],
            [0.99, 0],
            [1.25, 1],
            [true, 1],
            [false, 0],
            [null, null]
        ];
    }

    /**
     * @return array
     */
    public function providerNullableStringField()
    {
        return [
            ['test', 'test'],
            [123, '123'],
            [0.25, '0.25'],
            [true, '1'],
            [null, null]
        ];
    }

    /**
     * @return array
     */
    public function providerBaseModelDefaultData()
    {
        return [[[
            'string_property' => null,
            'integer_property' => null,
            'float_property' => null,
            'boolean_property' => null,
            'relation_property' => [
                'foo' => null,
                'bar' => 'zyx'
            ],
            'with_default_value_property' => 'default value',
        ]]];
    }

    /**
     * @return array
     */
    public function providerTestData()
    {
        return [
            [['foo' => 'bar', 'bar' => 'foo'], ['foo' => 'bar', 'bar' => 'foo']],
            [['foo' => 1, 'bar' => 2], ['foo' => '1', 'bar' => '2']]
        ];
    }

    /**
     * @return array
     */
    public function providerHasAttribute()
    {
        return [
            [['foo' => 'bar'], 'foo', true],
            [[], 'foo', true],
            [[], 'hidden', false],
            [['hidden' => '123'], 'hidden', true],
        ];
    }
}
