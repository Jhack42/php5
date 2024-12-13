<?php

// config/carousel.php
return [
    'websocket_port' => env('WEBSOCKET_PORT', 6001),
    'items_per_page' => env('CAROUSEL_ITEMS_PER_PAGE', 10),
    'auto_advance' => env('CAROUSEL_AUTO_ADVANCE', true),
    'transition_time' => env('CAROUSEL_TRANSITION_TIME', 5000),
];
