<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = "Admin";
    case STAFF = "Staff";
    case USER = "User";

    public static function getAuthorizedRoles(): string
    {
        return implode(
            '|',
            [
                self::ADMIN->value,
                self::STAFF->value,
                self::USER->value,
            ]
        );
    }
}
