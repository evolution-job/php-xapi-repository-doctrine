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

use XApi\Repository\Doctrine\Mapping\State;

/**
 * {@link State} repository interface definition.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface StateRepository
{
    /**
     * @return State The state or null if no matching state has been found
     */
    public function findState(array $criteria);

    /**
     * Saves a {@link State} in the underlying storage.
     *
     * @param State $state The state being stored
     * @param bool  $flush Whether or not to flush the managed objects
     *                     (i.e. write them to the data storage immediately)
     */
    public function storeState(State $state, bool $flush = true);
}
