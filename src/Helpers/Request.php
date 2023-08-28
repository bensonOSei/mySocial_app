<?php

namespace Benson\InforSharing\Helpers;

use Benson\InforSharing\Helpers\Traits\CanHandleRequests;

class Request
{
    use CanHandleRequests;

    public function __construct()
    {
        $this->__getAll();
        $this->interceptAll();
    }
}