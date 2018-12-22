<?php
namespace Modeler;

class PropertyMapper
{
    protected $data = [];
    protected static $rules = [];

    /**
     * ModelPropertyMapper constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $class
     * @return mixed
     */
    public function mapTo(string $class)
    {
        $result = static::mapProperties($this->data);

        if (class_exists($class) && method_exists($class, 'fromArray')) {
            /** @var Model $model */
            return call_user_func([$class, 'fromArray'], $result);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function mapToArray()
    {
        return static::mapProperties($this->data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function mapProperties(array $data)
    {
        $result = [];
        foreach (static::$rules as $key => $value) {
            static::preparedResultItem($result, $key, self::getValueFromArray($data, $value));
        }

        return $result;
    }

    /**
     * @param $array
     * @param $key
     * @param $value
     * @return mixed
     */
    protected static function preparedResultItem(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

    /**
     * @param array $array
     * @param string $key
     * @param string|null $default
     * @return mixed|null
     */
    protected static function getValueFromArray($array, $key, $default = null)
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValueFromArray($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }
}