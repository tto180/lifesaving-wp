<?php
/**
 * @license LGPL-2.1-or-later
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

class LearnDash_Reports_HTMLPurifier_URIFilter_Munge extends LearnDash_Reports_HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'Munge';

    /**
     * @type bool
     */
    public $post = true;

    /**
     * @type string
     */
    private $target;

    /**
     * @type LearnDash_Reports_HTMLPurifier_URIParser
     */
    private $parser;

    /**
     * @type bool
     */
    private $doEmbed;

    /**
     * @type string
     */
    private $secretKey;

    /**
     * @type array
     */
    protected $replace = array();

    /**
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @return bool
     */
    public function prepare($config)
    {
        $this->target = $config->get('URI.' . $this->name);
        $this->parser = new LearnDash_Reports_HTMLPurifier_URIParser();
        $this->doEmbed = $config->get('URI.MungeResources');
        $this->secretKey = $config->get('URI.MungeSecretKey');
        if ($this->secretKey && !function_exists('hash_hmac')) {
            throw new Exception("Cannot use %URI.MungeSecretKey without hash_hmac support.");
        }
        return true;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_URI $uri
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context)
    {
        if ($context->get('EmbeddedURI', true) && !$this->doEmbed) {
            return true;
        }

        $scheme_obj = $uri->getSchemeObj($config, $context);
        if (!$scheme_obj) {
            return true;
        } // ignore unknown schemes, maybe another postfilter did it
        if (!$scheme_obj->browsable) {
            return true;
        } // ignore non-browseable schemes, since we can't munge those in a reasonable way
        if ($uri->isBenign($config, $context)) {
            return true;
        } // don't redirect if a benign URL

        $this->makeReplace($uri, $config, $context);
        $this->replace = array_map('rawurlencode', $this->replace);

        $new_uri = strtr($this->target, $this->replace);
        $new_uri = $this->parser->parse($new_uri);
        // don't redirect if the target host is the same as the
        // starting host
        if ($uri->host === $new_uri->host) {
            return true;
        }
        $uri = $new_uri; // overwrite
        return true;
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_URI $uri
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     */
    protected function makeReplace($uri, $config, $context)
    {
        $string = $uri->toString();
        // always available
        $this->replace['%s'] = $string;
        $this->replace['%r'] = $context->get('EmbeddedURI', true) ?: '';
        $token = $context->get('CurrentToken', true) ?: '';
        $this->replace['%n'] = $token ? $token->name : '';
        $this->replace['%m'] = $context->get('CurrentAttr', true) ?: '';
        $this->replace['%p'] = $context->get('CurrentCSSProperty', true) ?: '';
        // not always available
        if ($this->secretKey) {
            $this->replace['%t'] = hash_hmac("sha256", $string, $this->secretKey);
        }
    }
}

// vim: et sw=4 sts=4
