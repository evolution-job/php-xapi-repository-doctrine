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
use Xabbuh\XApi\DataFixtures\StatementFixtures;
use XApi\Repository\Doctrine\Mapping\Statement;
use XApi\Repository\Doctrine\Repository\Mapping\StatementRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StatementRepositoryTest extends TestCase
{
    private MockObject $mockObject;

    private StatementRepository $statementRepository;

    protected function setUp(): void
    {
        $this->mockObject = $this->createObjectManagerMock();
        $mockObject = $this->createUnitOfWorkMock();
        
        $classMetadata = $this->createClassMetadataMock();
        $this->statementRepository = $this->createMappedStatementRepository($this->mockObject, $mockObject, $classMetadata);
    }

    public function testStatementDocumentIsPersisted(): void
    {
        $this->mockObject->expects($this->once())->method('persist')->with($this->isInstanceOf(Statement::class));

        $mappedStatement = Statement::fromModel(StatementFixtures::getMinimalStatement());
        $this->statementRepository->storeStatement($mappedStatement, true);
    }

    public function testFlushIsCalledByDefault(): void
    {
        $this->mockObject->expects($this->once())->method('flush');

        $mappedStatement = Statement::fromModel(StatementFixtures::getMinimalStatement());
        $this->statementRepository->storeStatement($mappedStatement);
    }

    public function testCallToFlushCanBeSuppressed(): void
    {
        $this->mockObject->expects($this->never())->method('flush');

        $mappedStatement = Statement::fromModel(StatementFixtures::getMinimalStatement());
        $this->statementRepository->storeStatement($mappedStatement, false);
    }

    abstract protected function getObjectManagerClass();

    protected function createObjectManagerMock(): MockObject
    {
        return $this->getMockBuilder($this->getObjectManagerClass())->disableOriginalConstructor()->getMock();
    }

    abstract protected function getUnitOfWorkClass();

    protected function createUnitOfWorkMock(): MockObject
    {
        return $this->getMockBuilder($this->getUnitOfWorkClass())->disableOriginalConstructor()->getMock();
    }

    abstract protected function getClassMetadataClass();

    protected function createClassMetadataMock(): MockObject
    {
        return $this->getMockBuilder($this->getClassMetadataClass())->disableOriginalConstructor()->getMock();
    }

    abstract protected function createMappedStatementRepository($objectManager, $unitOfWork, $classMetadata);
}
