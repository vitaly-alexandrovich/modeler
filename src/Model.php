<?php
namespace Modeler;

use Modeler\Exceptions\NotNullException;
use Modeler\Properties\BaseProperty;

class Model
{
    const PROPERTY_RAW = 'raw';
    const PROPERTY_STRING = 'string';
    const PROPERTY_INTEGER = 'integer';
    const PROPERTY_FLOAT = 'integer';
    const PROPERTY_BOOLEAN = 'boolean';

    protected $attributes = [];

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

        foreach (static::mapProperties() as $propertyName => $propertyType) {
            /** @var BaseProperty $propertyType */
            if (!isset($attributes[$propertyName])) {
                $attributes[$propertyName] = null;
            }

            try {
                $model->setAttribute($propertyName, static::castValue($attributes[$propertyName], $propertyType));
            } catch (NotNullException $e) {
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
     * @return bool|float|int|string
     * @throws NotNullException
     */
    protected static function castValue($value, $type) 
    {
        /** @var BaseProperty $type */
        return $type->prepareValue($value);
    }

    /**
     * @param string $attributeName
     * @param null $value
     */
    public function setAttribute(string $attributeName, $value = null)
    {
        $this->attributes[strtolower($attributeName)] = $value;
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
        return isset($this->attributes[$attributeName]);
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
                case('set'): return $this->getAttribute($attributeName, null);
            }
        }

        throw new \Exception("Unknown method ${$methodName}");
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