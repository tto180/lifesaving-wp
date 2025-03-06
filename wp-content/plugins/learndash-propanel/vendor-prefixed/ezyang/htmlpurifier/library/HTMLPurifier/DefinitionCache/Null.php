<?php

/**
 * Null cache object to use when no caching is on.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_DefinitionCache_Null extends LearnDash_Reports_HTMLPurifier_DefinitionCache
{

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function add($def, $config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function set($def, $config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Definition $def
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function replace($def, $config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function remove($config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function get($config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function flush($config)
    {
        return false;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function cleanup($config)
    {
        return false;
    }
}

// vim: et sw=4 sts=4
