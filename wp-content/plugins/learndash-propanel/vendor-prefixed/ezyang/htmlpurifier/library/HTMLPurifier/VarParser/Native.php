<?php

/**
 * This variable parser uses PHP's internal code engine. Because it does
 * this, it can represent all inputs; however, it is dangerous and cannot
 * be used by users.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_VarParser_Native extends LearnDash_Reports_HTMLPurifier_VarParser
{

    /**
     * @param mixed $var
     * @param int $type
     * @param bool $allow_null
     * @return null|string
     */
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $this->evalExpression($var);
    }

    /**
     * @param string $expr
     * @return mixed
     * @throws LearnDash_Reports_HTMLPurifier_VarParserException
     */
    protected function evalExpression($expr)
    {
        $var = null;
        $result = eval("\$var = $expr;");
        if ($result === false) {
            throw new LearnDash_Reports_HTMLPurifier_VarParserException("Fatal error in evaluated code");
        }
        return $var;
    }
}

// vim: et sw=4 sts=4
