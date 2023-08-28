<?php

declare(strict_types=1);

namespace Benson\InforSharing\Models;

class User extends Model
{
    protected string $table = 'users';

    protected array $columns = [
        'first_name',
        'last_name',
        'email',
        'password',
        'city',
        'region'
    ];

    protected array $sensitiveColumns = [
        'password'
    ];
}