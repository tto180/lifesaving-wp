<?php
/**
 * @license LGPL-2.1-or-later
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

class LearnDash_Reports_HTMLPurifier_URIFilter_DisableExternalResources extends LearnDash_Reports_HTMLPurifier_URIFilter_DisableExternal
{
    /**
     * @type string
     */
    public $name = 'DisableExternalResources';

    /**
     * @param LearnDash_Reports_HTMLPurifier_URI $uri
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context)
    {
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }
        return parent::filter($uri, $config, $context);
    }
}

// vim: et sw=4 sts=4
