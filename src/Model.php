<?php
namespace Modeler;

use BadMethodCallException;
use Modeler\Exceptions\NotNullException;
use Modeler\Exceptions\NotSpecifiedAttributeException;
use Modeler\Properties\BaseProperty;

class Model
{
    const PROPERTY_RAW = 'raw';
    const PROPERTY_STRING = 'string';
    const PROPERTY_INTEGER = 'integer';
    const PROPERTY_FLOAT = 'integer';
    const PROPERTY_BOOLEAN = 'boolean';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected static $availableNotSpecifiedAttributes = true;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        // Заполняем недостающие поля согласно заданным для них правилам
        foreach (static::mapProperties() as $propertyName => $propertyType) {
            /** @var BaseProperty $propertyType */
            $value = null;

            if ($propertyType->hasDefaultValue()) {
                $value = $propertyType->getDefaultValue();
            }

            try {
                $this->attributes[$propertyName] = $propertyType->prepareValue($value);
            } catch (NotNullException $e) {
                continue;
            }
        }
    }

    /**
     * @return array
     */
    protected static function mapProperties()
    {
        return [];
    }

    /**
     * @param array $attributes
     * @return static
     */
    public static function fromArray(array $attributes) 
    {
        $model = new static();

        foreach ($attributes as $attributeName => $attributeValue) {
            try {
                $model->setAttribute($attributeName, $attributeValue);
            } catch (NotNullException $exception) {
                continue;
            } catch (NotSpecifiedAttributeException $exception) {
                continue;
            }
        }

        return $model;
    }

    /**
     * @param string $json
     * @return static
     */
    public static function fromJson(string $json)
    {
        return static::fromArray(json_decode($json, true));
    }

    /**
     * @param $value
     * @param $type
     * @return mixed
     * @throws NotNullException
     */
    protected static function castValue($value, $type) 
    {
        /** @var BaseProperty $type */
        return $type->prepareValue($value);
    }

    /**
     * @param $attributeName
     * @return mixed|null
     */
    protected function getAttributeType($attributeName)
    {
        $properties = static::mapProperties();

        if (isset($properties[$attributeName])) {
            return $properties[$attributeName];
        }

        return null;
    }

    /**
     * @param string $attributeName
     * @param null $value
     * @return mixed|null
     * @throws NotNullException
     * @throws NotSpecifiedAttributeException
     */
    public function setAttribute(string $attributeName, $value = null)
    {
        $attributeName = strtolower($attributeName);
        $attributeType = $this->getAttributeType($attributeName);

        if (is_null($attributeType) and static::$availableNotSpecifiedAttributes === false) {
            throw new NotSpecifiedAttributeException("Attribute ${attributeName} is not specified in mapProperties method");
        }

        return $this->attributes[$attributeName] = !is_null($attributeType)
            ? static::castValue($value, $attributeType)
            : $value;
    }

    /**
     * @param string $attributeName
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getAttribute(string $attributeName, $defaultValue = null)
    {
        return $this->hasAttribute($attributeName) ? $this->attributes[$attributeName] : $defaultValue;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function hasAttribute(string $attributeName)
    {
        return array_key_exists($attributeName, $this->attributes);
    }

    /**
     * @param $method
     * @param array $args
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function __call($method, $args = [])
    {
        preg_match('/(has|get|set)([A-Za-z_]*)/', $method, $matches);

        if (count($matches) < 3) {
            throw new \Exception('Unknown method');
        }

        list($methodName, $action, $attribute) = $matches;

        $camelCaseAttribute = self::camelCaseToUnderscore($attribute);
        $attribute = strtolower($attribute);

        foreach([$attribute, $camelCaseAttribute] as $attributeName) {
            if ($attributeName === $attribute && !$this->hasAttribute($attributeName)) {
                continue;
            }

            switch ($action) {
                case('has'): return $this->hasAttribute($attributeName);
                case('get'): return $this->getAttribute($attributeName, null);
                case('set'): return $this->setAttribute($attributeName, null);
            }
        }

        throw new BadMethodCallException("Instance method Model->${methodName}() doesn't exist");
    }

    /**
     * @param $string
     * @return string
     */
    private static function camelCaseToUnderscore($string)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return strtolower(implode('_', $ret));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->attributes as $attributeName => $attributeValue) {
            $result[$attributeName] = ($attributeValue instanceof Model) ? $attributeValue->toArray() : $attributeValue;
        }

        return $result;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}