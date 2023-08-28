<?php

namespace Benson\InforSharing\Database;

use Benson\InforSharing\Helpers\Traits\CanQuery;

class Schema
{
    use CanQuery;

    private string $table;
    private array $columns;

    public function __construct($table = null)
    {
        if ($table) $this->table = $table;
    }

    public function id(string $constraint)
    {
        
    }


}
