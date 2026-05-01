<?php

namespace App\Support;

class OrganizationSession
{
    private const SESSION_KEY = 'current_organization_id';

    public static function setCurrentOrg($organizationId)
    {
        session([self::SESSION_KEY => $organizationId]);
    }

    public static function getCurrentOrg()
    {
        return session(self::SESSION_KEY);
    }

    public static function clearCurrentOrg()
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function hasCurrentOrg()
    {
        return session()->has(self::SESSION_KEY);
    }
}
