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
                'icon'  => 'fa-money',
                'route' => 'seat_tax.index',
                'permission'    => 'seat_tax.view',
            ],
            [
                'name' => '设置',
                'icon' => 'fa-gear',
                'route' => 'seat_tax.settings',
                'permission' => 'seat_tax.view',
            ],
            [
                'name'  => '关于',
                'icon'  => 'fa-info-circle',
                'route' => 'seat_tax.about',
                'permission' => 'seat_tax.view',
            ],
        ],
    ],
];
