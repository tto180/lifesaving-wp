<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Xlfn
{
    const XLFNREGEXP = '/(?<!_xlfn[.])\\b('
            // functions added with Excel 2010
        . 'beta[.]dist'
        . '|beta[.]inv'
        . '|binom[.]dist'
        . '|binom[.]inv'
        . '|ceiling[.]precise'
        . '|chisq[.]dist'
        . '|chisq[.]dist[.]rt'
        . '|chisq[.]inv'
        . '|chisq[.]inv[.]rt'
        . '|chisq[.]test'
        . '|confidence[.]norm'
        . '|confidence[.]t'
        . '|covariance[.]p'
        . '|covariance[.]s'
        . '|erf[.]precise'
        . '|erfc[.]precise'
        . '|expon[.]dist'
        . '|f[.]dist'
        . '|f[.]dist[.]rt'
        . '|f[.]inv'
        . '|f[.]inv[.]rt'
        . '|f[.]test'
        . '|floor[.]precise'
        . '|gamma[.]dist'
        . '|gamma[.]inv'
        . '|gammaln[.]precise'
        . '|lognorm[.]dist'
        . '|lognorm[.]inv'
        . '|mode[.]mult'
        . '|mode[.]sngl'
        . '|negbinom[.]dist'
        . '|networkdays[.]intl'
        . '|norm[.]dist'
        . '|norm[.]inv'
        . '|norm[.]s[.]dist'
        . '|norm[.]s[.]inv'
        . '|percentile[.]exc'
        . '|percentile[.]inc'
        . '|percentrank[.]exc'
        . '|percentrank[.]inc'
        . '|poisson[.]dist'
        . '|quartile[.]exc'
        . '|quartile[.]inc'
        . '|rank[.]avg'
        . '|rank[.]eq'
        . '|stdev[.]p'
        . '|stdev[.]s'
        . '|t[.]dist'
        . '|t[.]dist[.]2t'
        . '|t[.]dist[.]rt'
        . '|t[.]inv'
        . '|t[.]inv[.]2t'
        . '|t[.]test'
        . '|var[.]p'
        . '|var[.]s'
        . '|weibull[.]dist'
        . '|z[.]test'
        // functions added with Excel 2013
        . '|acot'
        . '|acoth'
        . '|arabic'
        . '|averageifs'
        . '|binom[.]dist[.]range'
        . '|bitand'
        . '|bitlshift'
        . '|bitor'
        . '|bitrshift'
        . '|bitxor'
        . '|ceiling[.]math'
        . '|combina'
        . '|cot'
        . '|coth'
        . '|csc'
        . '|csch'
        . '|days'
        . '|dbcs'
        . '|decimal'
        . '|encodeurl'
        . '|filterxml'
        . '|floor[.]math'
        . '|formulatext'
        . '|gamma'
        . '|gauss'
        . '|ifna'
        . '|imcosh'
        . '|imcot'
        . '|imcsc'
        . '|imcsch'
        . '|imsec'
        . '|imsech'
        . '|imsinh'
        . '|imtan'
        . '|isformula'
        . '|iso[.]ceiling'
        . '|isoweeknum'
        . '|munit'
        . '|numbervalue'
        . '|pduration'
        . '|permutationa'
        . '|phi'
        . '|rri'
        . '|sec'
        . '|sech'
        . '|sheet'
        . '|sheets'
        . '|skew[.]p'
        . '|unichar'
        . '|unicode'
        . '|webservice'
        . '|xor'
        // functions added with Excel 2016
        . '|forecast[.]et2'
        . '|forecast[.]ets[.]confint'
        . '|forecast[.]ets[.]seasonality'
        . '|forecast[.]ets[.]stat'
        . '|forecast[.]linear'
        . '|switch'
        // functions added with Excel 2019
        . '|concat'
        . '|countifs'
        . '|ifs'
        . '|maxifs'
        . '|minifs'
        . '|sumifs'
        . '|textjoin'
        // functions added with Excel 365
        . '|filter'
        . '|randarray'
        . '|sequence'
        . '|sort'
        . '|sortby'
        . '|unique'
        . '|xlookup'
        . '|xmatch'
        . '|arraytotext'
        . '|call'
        . '|let'
        . '|register[.]id'
        . '|valuetotext'
        . ')(?=\\s*[(])/i';

    /**
     * Prefix function name in string with _xlfn. where required.
     */
    public static function addXlfn(string $funcstring): string
    {
        return preg_replace(self::XLFNREGEXP, '_xlfn.$1', $funcstring);
    }

    /**
     * Prefix function name in string with _xlfn. where required.
     * Leading character, expected to be equals sign, is stripped.
     */
    public static function addXlfnStripEquals(string $funcstring): string
    {
        return self::addXlfn(substr($funcstring, 1));
    }
}
