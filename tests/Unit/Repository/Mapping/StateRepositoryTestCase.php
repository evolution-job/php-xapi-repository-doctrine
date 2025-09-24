<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Unit\Repository\Mapping;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\StateFixtures;
use XApi\Repository\Doctrine\Mapping\State;
use XApi\Repository\Doctrine\Repository\Mapping\StateRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StateRepositoryTestCase extends TestCase
{
    private MockObject $mockObject;

    private StateRepository $statementRepository;

    protected function setUp(): void
    {
        $this->mockObject = $this->createObjectManagerMock();
        $mockObject = $this->createUnitOfWorkMock();

        $classMetadata = $this->createClassMetadataMock();
        $this->statementRepository = $this->createMappedStateRepository($this->mockObject, $mockObject, $classMetadata);
    }

    public function testStateIsPersisted(): void
    {
        $this->mockObject->expects($this->once())->method('persist')->with($this->isInstanceOf(State::class));

        $mappedState = State::fromModel(StateFixtures::getMinimalState());
        $this->statementRepository->storeState($mappedState);
    }

    public function testFlushIsCalledByDefault(): void
    {
        $this->mockObject->expects($this->once())->method('flush');

        $mappedState = State::fromModel(StateFixtures::getMinimalState());
        $this->statementRepository->storeState($mappedState);
    }

    public function testCallToFlushCanBeSuppressed(): void
    {
        $this->mockObject->expects($this->never())->method('flush');

        $mappedState = State::fromModel(StateFixtures::getMinimalState());
        $this->statementRepository->storeState($mappedState, false);
    }

    abstract protected function getObjectManagerClass();

    protected function createObjectManagerMock(): MockObject
    {
        return $this->createMock($this->getObjectManagerClass());
    }

    abstract protected function getUnitOfWorkClass();

    protected function createUnitOfWorkMock(): MockObject
    {
        return $this->createMock($this->getUnitOfWorkClass());
    }

    abstract protected function getClassMetadataClass();

    protected function createClassMetadataMock(): MockObject
    {
        return $this->createMock($this->getClassMetadataClass());
    }

    abstract protected function createMappedStateRepository($objectManager, $unitOfWork, $classMetadata);
}
