<?php

/**
 * Validates ftp (File Transfer Protocol) URIs as defined by generic RFC 1738.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_URIScheme_ftp extends LearnDash_Reports_HTMLPurifier_URIScheme
{
    /**
     * @type int
     */
    public $default_port = 21;

    /**
     * @type bool
     */
    public $browsable = true; // usually

    /**
     * @type bool
     */
    public $hierarchical = true;

    /**
     * @param LearnDash_Reports_HTMLPurifier_URI $uri
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool
     */
    public function doValidate(&$uri, $config, $context)
    {
        $uri->query = null;

        // typecode check
        $semicolon_pos = strrpos($uri->path, ';'); // reverse
        if ($semicolon_pos !== false) {
            $type = substr($uri->path, $semicolon_pos + 1); // no semicolon
            $uri->path = substr($uri->path, 0, $semicolon_pos);
            $type_ret = '';
            if (strpos($type, '=') !== false) {
                // figure out whether or not the declaration is correct
                list($key, $typecode) = explode('=', $type, 2);
                if ($key !== 'type') {
                    // invalid key, tack it back on encoded
                    $uri->path .= '%3B' . $type;
                } elseif ($typecode === 'a' || $typecode === 'i' || $typecode === 'd') {
                    $type_ret = ";type=$typecode";
                }
            } else {
                $uri->path .= '%3B' . $type;
            }
            $uri->path = str_replace(';', '%3B', $uri->path);
            $uri->path .= $type_ret;
        }
        return true;
    }
}

// vim: et sw=4 sts=4
