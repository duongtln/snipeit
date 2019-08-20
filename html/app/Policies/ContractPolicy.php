<?php

namespace App\Policies;

use App\Policies\SnipePermissionsPolicy;

class ContractPolicy extends SnipePermissionsPolicy
{
    protected function columnName()
    {
        return 'contracts';
    }
}