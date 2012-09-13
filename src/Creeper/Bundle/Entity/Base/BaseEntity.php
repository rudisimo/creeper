<?php

/*
 * This file is part of the Creeper package.
 *
 * (c) Rodolfo Puig <rpuig@7gstudios.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Creeper\Bundle\Entity\Base;

class BaseEntity {
    /**
     * Sets a persistent fields value.
     *
     * @throws BadMethodCallException - When no persistent field exists by that name.
     * @param string $field
     * @param array $args
     * @return void
     */
    private function set($field, array $args)
    {
        if (property_exists($this, $field)) {
            $this->$field = $args[0];
        } else {
            throw new \BadMethodCallException("no field with name '".$field."' exists on '".$this->name()."'");
        }
    }

    /**
     * Get persistent field value.
     *
     * @throws BadMethodCallException - When no persistent field exists by that name.
     * @param string $field
     * @return mixed
     */
    private function get($field)
    {
        if (property_exists($this, $field)) {
            return $this->$field;
        } else {
            throw new \BadMethodCallException("no field with name '".$field."' exists on '".$this->name()."'");
        }
    }

    /**
     * Magic method that implements a setter/getter for all object properties.
     *
     * @throws BadMethodCallException - When an invalid method is called.
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args) {
        $command = substr($method, 0, 3);
        $field = lcfirst(substr($method, 3));
        if ($command == "set") {
            $this->set($field, $args);
        } else if ($command == "get") {
            return $this->get($field);
        } else {
            throw new \BadMethodCallException("There is no method ".$method." on ".$this->name());
        }
    }

    /**
     * Creates an unique identifier for the object.
     *
     * @return string
     */
    public function name() {
        return get_class($this);
    }

    /**
     * Creates a JSON string from the object properties.
     *
     * @return string
     */
    public function json() {
        return json_encode(get_object_vars($this));
    }
}