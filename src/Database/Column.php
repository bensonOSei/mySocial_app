<?php

namespace Benson\InforSharing\Database;

use Benson\InforSharing\Helpers\Traits\CanQuery;

class Column
{
    use CanQuery;
    private Constraint $constraint;
    private string $name;

    public function __construct(string $name = null)
    {
        if($name) $this->name = $name;

        $this->constraint = new Constraint();
    }

    public static function primary(string $columnName = 'id')
    {
        $col = new self($columnName);


        return $col;
    }

    public function createQuery()
    {
        $columnQuery = "";
        $columnQuery .= $this->name . " ";


        return $columnQuery;
    }
}
