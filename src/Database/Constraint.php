<?php

namespace Benson\InforSharing\Database;

class Constraint 
{
    public array $constraints = [
        'length' => 255,
        'primary' => null,
        'unique' => false,
        'foreign' => false,
        'default' => null,
        'nullable' => true,
        'type' => false,
        'auto_increment' => true
];

    public function __construct()
    {
        foreach ($this->constraints as $constraint => $value) {
            $this->{$constraint} = $value;
        }

    }
}
