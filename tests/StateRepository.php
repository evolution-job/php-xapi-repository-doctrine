<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests;

use Doctrine\Persistence\ObjectManager;
use TypeError;
use Xabbuh\XApi\Model\State;
use XApi\Repository\Api\StateRepositoryInterface;

/**
 * State repository clearing the object manager between read and write operations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final readonly class StateRepository implements StateRepositoryInterface
{
    private ObjectManager $objectManager;

    public function __construct(private StateRepositoryInterface $stateRepository, $objectManager)
    {
        if (!$objectManager instanceof ObjectManager) {
            throw new TypeError(sprintf('The second argument of %s() must be an instance of %s (%s given).', __METHOD__, ObjectManager::class, get_debug_type($objectManager)));
        }

        $this->objectManager = $objectManager;
    }

    public function findState(State $state): ?State
    {
        return $this->stateRepository->findState($state);
    }

    public function findStates(State $state): array
    {
        return $this->stateRepository->findStates($state);
    }

    public function removeState(State $state, bool $flush = true): void
    {
        $this->stateRepository->removeState($state);
    }

    public function storeState(State $state, bool $flush = true): void
    {
        $this->stateRepository->storeState($state, $flush);
    }
}
