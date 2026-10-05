<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace XApi\Repository\Doctrine\Repository\Mapping;

use XApi\Repository\Doctrine\Mapping\Verb;

/**
 * {@link Verb} repository interface definition.
 */
interface VerbRepository
{
    public function findVerb(array $criteria): ?Verb;
}
