<?php

/**
 * Validates HTTP status codes
 *
 * @since 1.0.0
 */
add_filter('validate_http_status', function ($http_status) {
    // List of accepted HTTP status codes
    $defaults = [
        200 => 'OK',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

    return (array_key_exists($http_status, $defaults)) ? $http_status : 200;
});

/**
 * Validate login notice
 *
 * @since 1.0.0
 */
add_filter('validate_notice', function ($notice) {
    return preg_replace('/[^a-z_\-0-9 ]/i', '', $notice);;
});
