<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\Matrix\Operators;

use LearnDash\Reports\Matrix\Div0Exception;
use LearnDash\Reports\Matrix\Exception;
use \LearnDash\Reports\Matrix\Matrix;
use \LearnDash\Reports\Matrix\Functions;

class Division extends Multiplication
{
    /**
     * Execute the division
     *
     * @param mixed $value The matrix or numeric value to divide the current base value by
     * @throws Exception If the provided argument is not appropriate for the operation
     * @return $this The operation object, allowing multiple divisions to be chained
     **/
    public function execute($value, string $type = 'division'): Operator
    {
        if (is_array($value)) {
            $value = new Matrix($value);
        }

        if (is_object($value) && ($value instanceof Matrix)) {
            $value = Functions::inverse($value, $type);

            return $this->multiplyMatrix($value, $type);
        } elseif (is_numeric($value)) {
            return $this->multiplyScalar(1 / $value, $type);
        }

        throw new Exception('Invalid argument for division');
    }
}
