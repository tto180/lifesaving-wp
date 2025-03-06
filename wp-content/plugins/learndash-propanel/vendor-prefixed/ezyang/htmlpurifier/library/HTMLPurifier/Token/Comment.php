<?php

/**
 * Concrete comment token class. Generally will be ignored.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_Token_Comment extends LearnDash_Reports_HTMLPurifier_Token
{
    /**
     * Character data within comment.
     * @type string
     */
    public $data;

    /**
     * @type bool
     */
    public $is_whitespace = true;

    /**
     * Transparent constructor.
     *
     * @param string $data String comment data.
     * @param int $line
     * @param int $col
     */
    public function __construct($data, $line = null, $col = null)
    {
        $this->data = $data;
        $this->line = $line;
        $this->col = $col;
    }

    public function toNode() {
        return new LearnDash_Reports_HTMLPurifier_Node_Comment($this->data, $this->line, $this->col);
    }
}

// vim: et sw=4 sts=4
