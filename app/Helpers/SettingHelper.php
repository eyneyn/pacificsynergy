<?php

use App\Models\Setting;

if (! function_exists('getCompanyName')) {
    function getCompanyName(): string
    {
        return Setting::value('company_name') ?? config('app.name');
    }
}