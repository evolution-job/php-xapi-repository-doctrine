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

    public function testNotFindStateByCriteria(): void
    {
        $state = StateFixtures::getMinimalState();
        $criteria = [
            'activityId' => $state->getActivity()->getId(),
            'stateId'    => $state->getStateId(),
        ];

        $this->mappedStateRepository->expects($this->once())->method('findState')->with($criteria)->willReturn(null);

        $state = $this->stateRepository->findState($criteria);

        $this->assertEmpty($state);
    }

    public function testFindStateByCriteria(): void
    {
        $state = StateFixtures::getTypicalState();
        $criteria = [
            'activity'     => $state->getActivity(),
            'stateId'        => $state->getStateId(),
            'registrationId' => $state->getRegistrationId(),
        ];

        $this->mappedStateRepository->expects($this->once())->method('findState')->with($criteria)->willReturn(
            MappedState::fromModel($state)
        );

        /** @var State $foundState */
        $foundState = $this->stateRepository->findState($criteria);

        $this->assertSame($state->getActivity()->getId()->getValue(), $foundState->getActivity()->getId()->getValue());
        $this->assertSame($state->getStateId(), $foundState->getStateId());
        $this->assertSame($state->getRegistrationId(), $foundState->getRegistrationId());
        $this->assertSame($state->getData(), $foundState->getData());
    }

    public function testSave(): void
    {
        $state = StateFixtures::getTypicalState();

        $this->mappedStateRepository->expects($this->once())->method('storeState')
            ->with($this->callback(static function (MappedState $actual) use ($state): bool {
            $expected = MappedState::fromModel($state);

            return $expected == $actual;
        }), true);

        $this->stateRepository->storeState($state);
    }

    public function testSaveWithoutFlush(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->mappedStateRepository->expects($this->once())->method('storeState')
            ->with($this->callback(static function (MappedState $actual) use ($state): bool {
            $expected = MappedState::fromModel($state);

            return $expected == $actual;
        }), false);

        $this->stateRepository->storeState($state, false);
    }

    protected function createMappedStateRepositoryMock(): MappedStateRepository|MockObject
    {
        return $this->createMock(MappedStateRepository::class);
    }
}
