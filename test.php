<?php
use Benson\InforSharing\Database\Column;

require_once 'vendor/autoload.php';

$column = Column::primary()->createQuery();

echo $column;