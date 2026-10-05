<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace XApi\Repository\Doctrine\Repository\Mapping;

use XApi\Repository\Doctrine\Mapping\StatementObject;

/**
 * {@link Object} repository interface definition.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
interface StatementObjectRepository
{
    /**
     * @return StatementObject|null The object or null if no matching object
     *                         has been found
     */
    public function findObject(array $criteria): ?StatementObject;
}
