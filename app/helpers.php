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
