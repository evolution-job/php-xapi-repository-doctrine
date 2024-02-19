<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Storage;

use XApi\Repository\Doctrine\Mapping\StatementObject;

/**
 * {@link Object} repository interface definition.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
interface StatementObjectStorage
{
    /**
     * @param array $criteria
     *
     * @return StatementObject The object or null if no matching object
     *                         has been found
     */
    public function findObject(array $criteria);
}