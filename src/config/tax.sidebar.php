<?php

return [
    'seat_tax' => [
        'name'          => 'SeAT Tax',
        'icon'          => 'fa-group',
        'route_segment' => 'seatgroups',
        'permission'    => 'seatgroups.view',
        'entries' => [
            [
                'name'  => 'SeAT Groups',
                'icon'  => 'fa-gear',
                'route' => 'seatgroups.index',
                'permission'    => 'seatgroups.view',
            ],
            [
                'name'  => 'About',
                'icon'  => 'fa-info-circle',
                'permission' => 'seatgroups.view',
                'route' => 'seatgroups.about',
            ],
        ],
    ],
];
