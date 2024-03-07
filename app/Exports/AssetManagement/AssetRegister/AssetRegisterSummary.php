<?php

namespace App\Exports\AssetManagement\AssetRegister;

class AssetRegisterSummary
{
    public $description;
    public $total;

    public function getHeader($assetCategory):Array {
        $headerArray = array();
        array_push($headerArray,"Description");
        foreach ($assetCategory as $val) {
            array_push($headerArray,$val['financeCatDescription']);
        }
        array_push($headerArray,"Total");
        return $headerArray;
    }

    // Magic method to set properties dynamically
    public function __set($name, $value)
    {
        // Set the property
        $this->properties[$name] = $value;

        // Check if the setter method exists, and if not, create one dynamically
        if (!method_exists($this, 'set' . ucfirst($name))) {
            $this->createSetter($name);
        }
    }

    // Method to dynamically create a setter
    private function createSetter($name)
    {
        $setter = function ($value) use ($name) {
            $this->properties[$name] = $value;
        };

        $methodName = 'set' . ucfirst($name);
        $this->$methodName = \Closure::bind($setter, $this, static::class);
    }

    // Example getter method to retrieve property values
    public function getProperty($name)
    {
        return $this->properties[$name] ?? null;
    }

}
