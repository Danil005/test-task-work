<?php

return [
    'users' => [
        'create' => [
            'success' => 'User successfully created'
        ],
        'me'     => [
            'success' => 'Information about me received'
        ]
    ],
    'cars'  => [
        'create' => [
            'success' => 'You have successfully created a car',
            'errors'  => [
                'already_have_car'      => 'Do you already have a car',
                'already_have_car_user' => 'The user already has a car',
            ]
        ],
        'update' => [
            'success' => 'You have successfully updated a car',
            'errors' => [
                'can_change_only_my' => 'You can change information about your car only'
            ]
        ],
        'delete' => [
            'success' => [
                'soft'  => 'You have successfully deleted a car',
                'force' => 'You have successfully completely deleted a car'
            ],
            'errors'  => [
                'can_delete_only_my' => 'You can delete your car only'
            ]
        ],
        'get' => [
            'success' => 'You have successfully received car',
            'errors' => [
                'not_my_car' => 'You can\'t get information about someone else\'s car',
            ]
        ]
    ]
];
