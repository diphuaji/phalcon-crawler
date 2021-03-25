<?php

namespace App\Helpers;

class UrlHelper
{
    public static function isInternal(string $baseUrl, string $url): bool
    {
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        $host = parse_url($url, PHP_URL_HOST);
        return (bool)(empty($host) || $baseHost === $host);
    }

}