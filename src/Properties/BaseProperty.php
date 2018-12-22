<?php
namespace Modeler\Properties;

use Modeler\Exceptions\NotNullException;

class BaseProperty
{
    protected $type;
    protected $availableNull = true;
    protected $defaultValue = null;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailableNull()
    {
        return $this->availableNull;
    }

    /**
     * @return static
     */
    public function notNull()
    {
        $this->availableNull = false;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     * @return static
     */
    public function defaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     * @throws NotNullException
     */
    public function prepareValue($value)
    {
        if (is_null($value)) {
            if (!is_null($this->getDefaultValue())) {
                $value = $this->getDefaultValue();
            }
        }

        if (is_null($value) && !$this->isAvailableNull()) {
            throw new NotNullException('Value should not be null');
        }

        return $value;
    }
}