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
use Xabbuh\XApi\DataFixtures\StatementFixtures;
use Xabbuh\XApi\DataFixtures\VerbFixtures;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementsFilter;
use Xabbuh\XApi\Model\Uuid as ModelUuid;
use XApi\Repository\Doctrine\Mapping\Statement as MappedStatement;
use XApi\Repository\Doctrine\Repository\Mapping\StatementRepository as MappedStatementRepository;
use XApi\Repository\Doctrine\Repository\StatementRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementRepositoryTest extends TestCase
{
    private MockObject|MappedStatementRepository $mappedStatementRepository;

    private StatementRepository $statementRepository;

    protected function setUp(): void
    {
        $this->mappedStatementRepository = $this->createMappedStatementRepositoryMock();
        $this->statementRepository = new StatementRepository($this->mappedStatementRepository);
    }

    public function testFindStatementById(): void
    {
        $statementId = StatementId::fromUuid(ModelUuid::uuid4());

        $this->mappedStatementRepository->expects($this->once())->method('findStatement')->with(['id' => $statementId->getValue()])->willReturn(MappedStatement::fromModel(StatementFixtures::getMinimalStatement()));

        $this->statementRepository->findStatementById($statementId);
    }

    public function testFindStatementsByCriteria(): void
    {
        $verb = VerbFixtures::getTypicalVerb();

        $this->mappedStatementRepository->expects($this->once())->method('findStatements')->with(['verb' => $verb->getId()->getValue()])->willReturn([]);

        $statementsFilter = new StatementsFilter();
        $statementsFilter->byVerb($verb);
        
        $this->statementRepository->findStatementsBy($statementsFilter);
    }

    public function testSave(): void
    {
        $statement = StatementFixtures::getMinimalStatement();
        $this->mappedStatementRepository->expects($this->once())->method('storeStatement')->with($this->callback(static function (MappedStatement $mappedStatement) use ($statement) : bool {
            $expected = MappedStatement::fromModel($statement);
            $actual = clone $mappedStatement;
            $actual->stored = null;
            return $expected == $actual;
        }), true);

        $this->statementRepository->storeStatement($statement);
    }

    public function testSaveWithoutFlush(): void
    {
        $statement = StatementFixtures::getMinimalStatement();
        $this->mappedStatementRepository->expects($this->once())->method('storeStatement')->with($this->callback(static function (MappedStatement $mappedStatement) use ($statement) : bool {
            $expected = MappedStatement::fromModel($statement);
            $actual = clone $mappedStatement;
            $actual->stored = null;
            return $expected == $actual;
        }), false);

        $this->statementRepository->storeStatement($statement, false);
    }

    protected function createMappedStatementRepositoryMock(): MappedStatementRepository|MockObject
    {
        return $this->getMockBuilder(MappedStatementRepository::class)->getMock();
    }
}
