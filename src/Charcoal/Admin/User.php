<?php

namespace Charcoal\Admin;

// Module `charcoal-base` dependencies
use Charcoal\User\AbstractUser;

/**
 * Admin User class.
 */
class User extends AbstractUser
{
    /**
     * @return string
     */
    public static function sessionKey()
    {
        return 'admin.user';
    }
}
