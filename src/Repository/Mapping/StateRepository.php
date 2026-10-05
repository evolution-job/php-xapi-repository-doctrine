<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace XApi\Repository\Doctrine\Repository\Mapping;

use DateTimeImmutable;
use XApi\Repository\Doctrine\Mapping\State;

/**
 * {@link State} repository interface definition.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface StateRepository
{
    /**
     * @return State|null The state or null if no matching state has been found
     */
    public function findState(State $state): ?State;

    /**
     * @param DateTimeImmutable|null $since Return only States modified strictly after this timestamp
     * @return array States have been found
     */
    public function findStates(State $state, ?DateTimeImmutable $since = null): array;

    /**
     * @param bool $flush Whether or not to flush the managed objects
     *                       immediately (i.e. remove them to the data storage)
     */
    public function removeState(State $state, bool $flush = true): void;

    /**
     * Saves a {@link State} in the underlying storage.
     *
     * @param State $state The state being stored
     * @param bool $flush Whether or not to flush the managed objects
     *                     (i.e. write them to the data storage immediately)
     */
    public function storeState(State $state, bool $flush = true): void;
}
