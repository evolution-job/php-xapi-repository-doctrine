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

use DateTime;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementsFilter;
use Xabbuh\XApi\Model\Uuid as ModelUuid;
use XApi\Repository\Api\StatementRepositoryInterface;
use XApi\Repository\Doctrine\Mapping\Statement as MappedStatement;
use XApi\Repository\Doctrine\Repository\Mapping\StatementRepository as BaseStatementRepository;

/**
 * Doctrine based {@link Statement} repository.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class StatementRepository implements StatementRepositoryInterface
{
    public function __construct(private readonly BaseStatementRepository $baseStatementRepository) { }

    /**
     * {@inheritdoc}
     */
    public function findStatementById(StatementId $statementId, Actor $actor = null): Statement
    {
        $criteria = ['id' => $statementId->getValue()];

        if ($actor instanceof Actor) {
            $criteria['authority'] = $actor;
        }

        $mappedStatement = $this->baseStatementRepository->findStatement($criteria);

        if (null === $mappedStatement) {
            throw new NotFoundException('No statements could be found matching the given criteria.');
        }

        $statement = $mappedStatement->getModel();

        if ($statement->isVoidStatement()) {
            throw new NotFoundException('The stored statement is a voiding statement.');
        }

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function findVoidedStatementById(StatementId $voidedStatementId, Actor $actor = null): Statement
    {
        $criteria = ['id' => $voidedStatementId->getValue()];

        if ($actor instanceof Actor) {
            $criteria['authority'] = $actor;
        }

        $mappedStatement = $this->baseStatementRepository->findStatement($criteria);

        if (null === $mappedStatement) {
            throw new NotFoundException('No voided statements could be found matching the given criteria.');
        }

        $statement = $mappedStatement->getModel();

        if (!$statement->isVoidStatement()) {
            throw new NotFoundException('The stored statement is no voiding statement.');
        }

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function findStatementsBy(StatementsFilter $statementsFilter, Actor $actor = null): array
    {
        $statementsFilter = $statementsFilter->getFilter();

        if ($actor instanceof Actor) {
            $statementsFilter['authority'] = $actor;
        }

        $mappedStatements = $this->baseStatementRepository->findStatements($statementsFilter);
        $statements = [];

        foreach ($mappedStatements as $mappedStatement) {
            $statements[] = $mappedStatement->getModel();
        }

        return $statements;
    }

    /**
     * {@inheritdoc}
     */
    public function storeStatement(Statement $statement, bool $flush = true): StatementId
    {
        if (!$statement->getId() instanceof StatementId) {

            $uuid = ModelUuid::uuid4();

            $statement = $statement->withId(StatementId::fromUuid($uuid));
        }

        $mappedStatement = MappedStatement::fromModel($statement);
        $mappedStatement->stored = new DateTime();

        $this->baseStatementRepository->storeStatement($mappedStatement, $flush);

        return $statement->getId();
    }
}
