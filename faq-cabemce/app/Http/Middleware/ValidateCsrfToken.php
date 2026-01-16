<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

class ValidateCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'livewire/*',
        'livewire/update',
        'livewire/upload-file',
        'livewire/preview-file/*',
        'storage/*',
        'admin/*',
    ];
}
