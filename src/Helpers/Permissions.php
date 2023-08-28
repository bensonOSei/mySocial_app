<?php

namespace Benson\InforSharing\Helpers;

use Benson\InforSharing\Controllers\Controller;

class Permissions
{


    public function ownsThis($id)
    {
        $controller = new Controller();
        $userId = $controller->auth()->id;

        return $userId === $id;
    }
}
