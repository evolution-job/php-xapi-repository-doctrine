<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Unit\Repository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\StateFixtures;
use Xabbuh\XApi\Model\State;
use XApi\Repository\Doctrine\Mapping\State as MappedState;
use XApi\Repository\Doctrine\Repository\Mapping\StateRepository as MappedStateRepository;
use XApi\Repository\Doctrine\Repository\StateRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StateRepositoryTest extends TestCase
{
    private MockObject|MappedStateRepository $mappedStateRepository;

    private StateRepository $stateRepository;

    protected function setUp(): void
    {
        $this->mappedStateRepository = $this->createMappedStateRepositoryMock();
        $this->stateRepository = new StateRepository($this->mappedStateRepository);
    }

    public function testNotFindState(): void
    {
        $state = StateFixtures::getMinimalState();

        $this->mappedStateRepository->expects($this->once())->method('findState')
            ->with(MappedState::fromModel($state))
            ->willReturn(null);

        $state = $this->stateRepository->findState($state);

        $this->assertEmpty($state);
    }

    public function testFindState(): void
    {
        $state = StateFixtures::getTypicalState();

        $this->mappedStateRepository->expects($this->once())->method('findState')
            ->with(MappedState::fromModel($state))
            ->willReturn(MappedState::fromModel($state)
        );

        /** @var State $foundState */
        $foundState = $this->stateRepository->findState($state);

        $this->assertTrue($state->equals($foundState));
    }

    public function testRemove(): void
    {
        $state = StateFixtures::getTypicalState();

        $this->mappedStateRepository->expects($this->once())->method('removeState')
            ->with(MappedState::fromModel($state), true);

        $this->stateRepository->removeState($state);
    }

    public function testRemoveWithoutFlush(): void
    {
        $state = StateFixtures::getTypicalState();

        $this->mappedStateRepository->expects($this->once())->method('removeState')
            ->with(MappedState::fromModel($state), false);

        $this->stateRepository->removeState($state, false);
    }

    public function testSave(): void
    {
        $state = StateFixtures::getTypicalState();

        $this->mappedStateRepository->expects($this->once())->method('storeState')
            ->with(MappedState::fromModel($state), true);

        $this->stateRepository->storeState($state);
    }

    public function testSaveWithoutFlush(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->mappedStateRepository->expects($this->once())->method('storeState')
            ->with(MappedState::fromModel($state), false);

        $this->stateRepository->storeState($state, false);
    }

    protected function createMappedStateRepositoryMock(): MappedStateRepository|MockObject
    {
        return $this->createMock(MappedStateRepository::class);
    }
}
