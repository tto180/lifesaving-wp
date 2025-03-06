<?php

/**
 * Injector that displays the URL of an anchor instead of linking to it, in addition to showing the text of the link.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_Injector_DisplayLinkURI extends LearnDash_Reports_HTMLPurifier_Injector
{
    /**
     * @type string
     */
    public $name = 'DisplayLinkURI';

    /**
     * @type array
     */
    public $needed = array('a');

    /**
     * @param $token
     */
    public function handleElement(&$token)
    {
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Token $token
     */
    public function handleEnd(&$token)
    {
        if (isset($token->start->attr['href'])) {
            $url = $token->start->attr['href'];
            unset($token->start->attr['href']);
            $token = array($token, new LearnDash_Reports_HTMLPurifier_Token_Text(" ($url)"));
        } else {
            // nothing to display
        }
    }
}

// vim: et sw=4 sts=4
