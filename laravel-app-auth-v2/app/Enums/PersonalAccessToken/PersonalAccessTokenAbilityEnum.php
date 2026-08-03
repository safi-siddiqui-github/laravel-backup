<?php

namespace App\Enums\PersonalAccessToken;

enum PersonalAccessTokenAbilityEnum: string
{
    case ALLOW_ALL = '*';
    case ALLOW_REGISTER_USER = 'ALLOW_REGISTER_USER';
    case ALLOW_GENERAL_USER = 'ALLOW_GENERAL_USER';
    case ALLOW_PASSWORD_RESET = 'ALLOW_PASSWORD_RESET';
}
