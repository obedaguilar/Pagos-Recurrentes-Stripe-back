<?php


return [


    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'plans' => [
            'monthly' => env('STRIPE_MONTHLY_PLAN'),
            'yearly' => env('STRIPE_YEARLY_PLAN'),
        ],
    ],
];
