<?php

namespace PHPLocals;


class Local
{
    private $parent;
    private $variables = array();

    public function __construct(Local $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return Local
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $ret_val = $default;
        if (isset($this->variables[$key])) {
            $ret_val = $this->variables[$key];
        } elseif (null !== $this->parent) {
            $ret_val = $this->parent->get($key, $default);
        }

        return $ret_val;
    }

    /**
     * Unset a variable in the local scope. This
     *  will _not_ remove it from the parent's
     *  scope.
     *
     * @param $key
     */
    public function del($key)
    {
        unset($this->variables[$key]);
    }

    /**
     * Check if variable exists in the current
     *  scope. This will also check the parent's
     *  scope if it's not found locally.
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        $exists = false;
        if (isset($this->variables[$key])) {
            $exists = true;
        } elseif (null !== $this->parent) {
            $exists = $this->parent->exists($key);
        }

        return $exists;
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __isset($key)
    {
        return $this->exists($key);
    }
}