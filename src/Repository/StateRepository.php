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
final readonly class StateRepository implements StateRepositoryInterface
{
    public function __construct(private BaseStateRepository $baseStateRepository) { }

    public function findState(State $state): ?State
    {
        $mappedState = MappedState::fromModel($state);

        return $this->baseStateRepository->findState($mappedState)?->getModel();
    }

    /**
     * @param State $state
     * @return array States if no matching states have been found
     */
    public function findStates(State $state): array
    {
        $mappedState = MappedState::fromModel($state);

        $states = $this->baseStateRepository->findStates($mappedState);

        $modelStates = [];
        foreach ($states as $foundState) {
            $modelStates[] = $foundState->getModel();
        }

        return $modelStates;
    }

    public function removeState(State $state, bool $flush = true): void
    {
        $mappedState = MappedState::fromModel($state);

        $this->baseStateRepository->removeState($mappedState, $flush);
    }

    public function storeState(State $state, bool $flush = true): void
    {
        $mappedState = MappedState::fromModel($state);

        $this->baseStateRepository->storeState($mappedState, $flush);
    }
}