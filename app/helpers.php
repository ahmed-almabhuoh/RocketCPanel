<?php

if (! function_exists('getRocketShMAPIKeys')) {
    function getRocketShMAPIKeys(): array
    {
        return [
            'public' => config('app.api_keys.public'),
            'secret' => config('app.api_keys.secret')
        ];
    }
}

if (! function_exists('getRocketShMAPILink')) {
    function getRocketShMAPILink(): string
    {
        $env_link = config('app.env') == 'local' ? config('app.api_links.rocket_shm_dashboard_localhost') : config('app.api_links.rocket_shm_dashboard_production');
        return $env_link . 'api/';
    }
}

if (!function_exists('returnWithKeyValuesArray')) {
    function returnWithKeyValuesArray(array $array, bool $returnOnlyKeys = false, bool $returnOnlyValues = false): array
    {
        $casted = [];

        foreach ($array as $key => $value) {
            $casted[lcfirst($value)] = __(ucfirst($value));
        }

        if ($returnOnlyKeys) {
            foreach ($array as $key => $value) {
                $casted[lcfirst($value)] = __(lcfirst($value));
            }
            return $casted;
        }

        if ($returnOnlyValues) {
            foreach ($array as $key => $value) {
                $casted[lcfirst($value)] = __(ucfirst($value));
            }
            return $casted;
        }

        return $casted;
    }
}

if (!function_exists('getCreditsLevel')) {
    function getCreditsLevel($vehicleSize): int
    {
        return  $vehicleSize == 'big' ? 3 : ($vehicleSize == 'middle' ? 2 : 1);
    }
}
