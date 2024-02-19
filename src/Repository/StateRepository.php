<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Repository;

use Xabbuh\XApi\Model\State;
use XApi\Repository\Api\StateRepositoryInterface;
use XApi\Repository\Doctrine\Mapping\State as MappedState;
use XApi\Repository\Doctrine\Repository\Mapping\StateRepository as BaseStateRepository;

/**
 * Doctrine based {@link State} repository.
 *
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final class StateRepository implements StateRepositoryInterface
{
    private $repository;

    public function __construct(BaseStateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $criteria
     * @return MappedState|object|null
     */
    public function findState(array $criteria)
    {
        return $this->repository->findState($criteria);
    }

    /**
     * @param State $state
     * @param bool $flush
     */
    public function storeState(State $state, $flush = true): void
    {
        $mappedState = MappedState::fromModel($state);

        $this->repository->storeState($mappedState);
    }
}