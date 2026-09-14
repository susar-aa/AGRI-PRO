<?php
/**
 * Application Configuration
 */

return [
    'app_name' => 'Agri Co-Op ERP',
    'app_env' => getenv('APP_ENV') ?: 'development',
    'debug' => getenv('APP_DEBUG') !== false ? (bool)getenv('APP_DEBUG') : true,
    'base_url' => getenv('APP_URL') ?: (
        (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'agripro.trycurtiss.com') !== false)
            ? 'https://agripro.trycurtiss.com'
            : '/AGRI%20PRO'
    ),
    'timezone' => 'Asia/Colombo',
    'session_name' => 'agri_erp_session',
    'currency' => 'LKR',
    'currency_symbol' => 'Rs. '
];
