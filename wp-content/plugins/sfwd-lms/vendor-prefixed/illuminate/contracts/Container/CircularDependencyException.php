<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Container;

use Exception;
use StellarWP\Learndash\Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
