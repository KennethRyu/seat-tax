<?php

return [
    'seat_tax' => [
        'name'          => 'SeAT税务',
        'icon'          => 'fa-group',
        'route_segment' => 'seat_tax',
        'permission'    => 'seat_tax.view',
        'entries' => [
            [
                'name'  => '税务账单',
                'icon'  => 'fa-gear',
                'permission'    => 'seat_tax.view',
                'route' => 'seat_tax.index',
            ],
            [
                'name'  => '关于',
                'icon'  => 'fa-info-circle',
                'permission' => 'seat_tax.view',
                'route' => 'seat_tax.about',
            ],
        ],
    ],
];
