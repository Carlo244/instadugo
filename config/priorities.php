<?php

/**
 * Blood Request Priority Configuration
 * 
 * Centralized configuration for priority levels, colors, icons, and styling
 */

return [
    'levels' => [
        'Emergency' => [
            'label' => 'Emergency',
            'icon' => 'bi-exclamation-triangle-fill',
            'color' => 'danger',
            'badge_class' => 'bg-danger',
            'button_class' => 'btn-priority-emergency',
            'icon_bg' => 'bg-danger text-white',
            'gradient' => 'linear-gradient(135deg, #e63946 0%, #9b1c1c 100%)',
            'order' => 1,
        ],
        'High' => [
            'label' => 'High',
            'icon' => 'bi-exclamation-circle-fill',
            'color' => 'warning',
            'badge_class' => 'bg-warning',
            'button_class' => 'btn-priority-high',
            'icon_bg' => 'bg-warning text-dark',
            'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'order' => 2,
        ],
        'Normal' => [
            'label' => 'Normal',
            'icon' => 'bi-info-circle-fill',
            'color' => 'success',
            'badge_class' => 'bg-success',
            'button_class' => 'btn-priority-normal',
            'icon_bg' => 'bg-success text-white',
            'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'order' => 3,
        ],
    ],

    'order' => ['Emergency', 'High', 'Normal'],
];
