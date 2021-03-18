<?php

namespace ByTIC\Auth\Models\Users\Traits;

use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUsersTrait;
use ByTIC\Auth\Models\Users\Traits\Persistance\PersistentUsersTrait;
use ByTIC\DataObjects\Behaviors\Timestampable\TimestampableManagerTrait;

/**
 * Class AbstractUsersTrait
 * @package ByTIC\Auth\Models\Users\AbstractUser
 */
trait AbstractUsersTrait
{
    use AuthenticationUsersTrait;
    use PersistentUsersTrait;
    use TimestampableManagerTrait;
}
