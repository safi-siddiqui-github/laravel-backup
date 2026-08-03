<?php

namespace App\Models;

use App\Enums\Otp\OtpTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Otp extends Model
{
    use HasApiTokens;

    protected function casts(): array
    {
        return [
            'type' => OtpTypeEnum::class,
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }
}
