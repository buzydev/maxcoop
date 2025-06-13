<?php

return [
    // @todo: this too should be moved to db
    'plans' => [
        0 => ['id' => 'INACTIVE', 'amount' => 0],
        1 => ['id' => 'PLATINUM', 'amount' => 500000],
        2 => ['id' => 'GOLD', 'amount' => 100000],
        3 => ['id' => 'SILVER', 'amount' => 50000],
        4 => ['id' => 'BRONZE', 'amount' => 25000]
    ],
    'accountStatus' => [0 => 'PENDING', 1 => 'SUCCESS', 2 => 'REJECTED'],
    'roles' => [0 => 'SUPER_ADMIN', 1 => 'ADMIN', 2 => 'MEMBER'],
    'earningType' => [['id' => 0, 'name' => 'ACCOUNT_ACTIVATION'],  ['id' => 1, 'name' => 'PROPERTY_SALE']],
    'admin_email' => env('ADMIN_EMAIL', null)
];
