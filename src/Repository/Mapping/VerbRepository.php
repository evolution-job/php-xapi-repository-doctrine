<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
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
    /**
     * @param array $criteria
     */
    public function findVerb(array $criteria): ?Verb;
}
