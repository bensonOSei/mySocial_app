<?php

namespace Benson\InforSharing\Database;

use Benson\InforSharing\Helpers\Traits\CanQuery;

class Table
{
    use CanQuery;
    
    private string $name;
    private array $columns;

}
