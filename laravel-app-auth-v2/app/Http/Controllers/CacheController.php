<?php

namespace App\Http\Controllers;

use App\Enums\Cache\CacheTypeEnum;
use App\Enums\Otp\OtpTypeEnum;
use App\Http\Resources\PersonalAccessToken\PersonalAccessTokenResource;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CacheController extends Controller
{
    public function __construct() {}

    public function createCacheEmailVerificationToken(): void
    {
        request()->validate([
            // 'type' => ['required', Rule::enum(CacheTypeEnum::class)],
            // 'email' => ['required', 'string', 'email', 'exists:users,email'],
            'email' => ['required', 'string', 'email'],
        ]);

        cache(
            [request()->input('email') => CacheTypeEnum::EMAIL_VERIFY_TOKEN],
            now()->addMinutes(10)
        );
    }

    public function getCacheEmailVerificationToken(): string
    {
        request()->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        return cache(request()->input('email'));
    }
}
