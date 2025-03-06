<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace LearnDash\Reports\ZipStream\Option;

use LearnDash\Reports\MyCLabs\Enum\Enum;

/**
 * Class Version
 * @package LearnDash\Reports\ZipStream\Option
 *
 * @method static STORE(): Version
 * @method static DEFLATE(): Version
 * @method static ZIP64(): Version
 * @psalm-immutable
 */
class Version extends Enum
{
    public const STORE = 0x000A; // 1.00

    public const DEFLATE = 0x0014; // 2.00

    public const ZIP64 = 0x002D; // 4.50
}
