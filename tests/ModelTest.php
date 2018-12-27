<?php
use Modeler\Model;
use Modeler\Property;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    /**
     * @param $attributeName
     * @param $attributeValue
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerGetAttribute
     */
    public function testGetAttribute($attributeName, $attributeValue)
    {
        $model = new Model();
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);
        $attributes->setValue($model, [$attributeName => $attributeValue]);

        $this->assertEquals($model->getAttribute($attributeName), $attributeValue);
    }

    /**
     * @return array
     */
    public function providerGetAttribute()
    {
        return [
            ['foo', 'bar'],
            ['bar', 'foo']
        ];
    }

    /**
     * @param $json
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerFromJson
     */
    public function testFromJson($json, $result)
    {
        $model = Model::fromJson($json);
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);

        $this->assertEquals($attributes->getValue($model), $result);
    }

    /**
     * @return array
     */
    public function providerFromJson()
    {
        return [
            [
                json_encode(['foo' => 'bar']),
                ['foo' => 'bar']
            ],
        ];
    }

    /**
     * @param $data
     * @param $attributeName
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerHasAttribute
     */
    public function testHasAttribute($data, $attributeName, $result)
    {
        $model = new Model();
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);
        $attributes->setValue($model, $data);

        $this->assertEquals($model->hasAttribute($attributeName), $result);
    }

    /**
     * @return array
     */
    public function providerHasAttribute()
    {
        return [
            [[], 'test', false],
            [['test'], 'test', false],
            [['test' => 'test'], 'test', true],
            [['test' => null], 'test', true],
            [['test' => false], 'test', true],
        ];
    }

    /**
     * @param $data
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerToArray
     */
    public function testToArray($data, $result)
    {
        $model = new Model();
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);
        $attributes->setValue($model, $data);

        $this->assertEquals($model->toArray(), $result);
    }

    /**
     * @return array
     */
    public function providerToArray()
    {
        return [
            [
                ['foo' => 'bar'],
                ['foo' => 'bar']
            ],
            [
                ['foo' => 'bar', 'nested' => Model::fromArray(['nested_property' => 123])],
                ['foo' => 'bar', 'nested' => ['nested_property' => 123]]
            ],
            [
                ['foo' => null],
                ['foo' => null]
            ],
        ];
    }

    /**
     * @param $data
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerToJson
     */
    public function testToJson($data, $result)
    {
        $model = new Model();
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);
        $attributes->setValue($model, $data);

        $this->assertEquals($model->toJson(), $result);
    }

    /**
     * @return array
     */
    public function providerToJson()
    {
        return array_map(function ($set) {
            return [
                $set[0],
                json_encode($set[1])
            ];
        }, $this->providerToArray());
    }

    /**
     * @param $mapProperties
     * @param $result
     * @param $assertType
     * @throws ReflectionException
     * @dataProvider provider__construct
     */
    public function test__construct($mapProperties, $result, $assertType)
    {
        $reflectionClass = new ReflectionClass(Model::class);

        /** @var Model $model */
        $model = $reflectionClass->newInstanceWithoutConstructor();

        $mapPropertiesClosure = new ReflectionProperty($model, 'mapPropertiesClosure');
        $mapPropertiesClosure->setAccessible(true);

        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);

        $mapPropertiesClosure->setValue($model, function () use ($mapProperties) {
            return $mapProperties;
        });

        $model->__construct();

        ([$this, 'assert' . ucfirst($assertType)])($attributes->getValue($model), $result);
    }

    /**
     * @return array
     */
    public function provider__construct()
    {
        return [
            [
                ['foo' => Property::string()],
                ['foo' => null],
                'same'
            ],
            [
                ['foo' => Property::string()->defaultValue('bar')],
                ['foo' => 'bar'],
                'same'
            ],
            [
                ['foo' => Property::relation(Model::class)->defaultValue(['nested' => 'bar'])],
                ['foo' => Model::fromArray(['nested' => 'bar'])],
                'equals'
            ],
            [
                ['foo' => Property::integer()->defaultValue('1')],
                ['foo' => 1],
                'same'
            ]
        ];
    }

    /**
     * @param $mapProperties
     * @param $attributeName
     * @param $attributeValue
     * @param $result
     * @param string $assertType
     * @throws ReflectionException
     * @throws \Modeler\Exceptions\NotNullException
     * @throws \Modeler\Exceptions\NotSpecifiedAttributeException
     *
     * @dataProvider providerSetProperty
     */
    public function testSetAttribute($mapProperties, $attributeName, $attributeValue, $result, $assertType = 'same')
    {
        $reflectionClass = new ReflectionClass(Model::class);

        /** @var Model $model */
        $model = $reflectionClass->newInstanceWithoutConstructor();

        $mapPropertiesClosure = new ReflectionProperty($model, 'mapPropertiesClosure');
        $mapPropertiesClosure->setAccessible(true);

        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);

        $mapPropertiesClosure->setValue($model, function () use ($mapProperties) {
            return $mapProperties;
        });

        $model->__construct();

        $model->setAttribute($attributeName, $attributeValue);

        ([$this, 'assert' . ucfirst($assertType)])($attributes->getValue($model)[$attributeName], $result);
    }

    /**
     * @return array
     */
    public function providerSetProperty()
    {
        return [
            [['foo' => Property::string()], 'foo', 'bar', 'bar',],
            [['foo' => Property::string()], 'foo', 1, '1',],
            [['foo' => Property::boolean()], 'foo', 'true', true,],
            [['foo' => Property::integer()], 'foo', '123', 123,],
            [['foo' => Property::relation(Model::class)], 'foo', ['a' => 1], Model::fromArray(['a' => 1]), 'equals'],
            [['foo' => Property::string()->defaultValue('bar')], 'foo', null, 'bar',],
            [['foo' => Property::string()->notNull()->defaultValue('bar')], 'foo', null, 'bar',],
        ];
    }

    /**
     * @param $array
     * @param $result
     * @throws ReflectionException
     * @throws Exception
     *
     * @dataProvider providerFromArray
     */
    public function testFromArray($array, $result)
    {
        $model = Model::fromArray($array);
        $attributes = new ReflectionProperty($model, 'attributes');
        $attributes->setAccessible(true);

        $this->assertEquals($attributes->getValue($model), $result);
    }

    /**
     * @return array
     */
    public function providerFromArray()
    {
        return [
            [
                ['foo' => 'bar'],
                ['foo' => 'bar']
            ],
        ];
    }
}
