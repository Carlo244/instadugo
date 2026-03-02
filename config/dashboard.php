<?php

/**
 * Dashboard Configuration
 * 
 * Centralized configuration for dashboard stats and metrics
 */

return [
    'stats' => [
        'users' => [
            'label' => 'Registered Users',
            'icon' => 'bi-people',
            'card_class' => 'stat-card-users',
            'color' => 'primary',
        ],
        'pending' => [
            'label' => 'Pending Requests',
            'icon' => 'bi-clock-history',
            'card_class' => 'stat-card-pending',
            'color' => 'warning',
        ],
        'fulfilled' => [
            'label' => 'Fulfilled Requests',
            'icon' => 'bi-check2-circle',
            'card_class' => 'stat-card-done',
            'color' => 'success',
        ],
        'appointments' => [
            'label' => "Today's Appointments",
            'icon' => 'bi-calendar-check',
            'card_class' => 'stat-card-appt',
            'color' => 'info',
        ],
    ],
];
