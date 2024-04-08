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
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementsFilter;
use XApi\Repository\Api\StatementRepositoryInterface;

/**
 * Statement repository clearing the object manager between read and write operations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class StatementRepository implements StatementRepositoryInterface
{
    
    private readonly ObjectManager $objectManager;

    public function __construct(private readonly StatementRepositoryInterface $statementRepository, $objectManager)
    {
        if (!$objectManager instanceof ObjectManager) {
            throw new TypeError(sprintf('The second argument of %s() must be an instance of %s (%s given).', __METHOD__, ObjectManager::class, get_debug_type($objectManager)));
        }
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findStatementById(StatementId $statementId, Actor $actor = null): Statement
    {
        $statement = $this->statementRepository->findStatementById($statementId, $actor);
        $this->objectManager->clear();

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function findVoidedStatementById(StatementId $voidedStatementId, Actor $actor = null): Statement
    {
        $statement = $this->statementRepository->findVoidedStatementById($voidedStatementId, $actor);
        $this->objectManager->clear();

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function findStatementsBy(StatementsFilter $statementsFilter, Actor $actor = null): array
    {
        $statements = $this->statementRepository->findStatementsBy($statementsFilter, $actor);
        $this->objectManager->clear();

        return $statements;
    }

    /**
     * {@inheritdoc}
     */
    public function storeStatement(Statement $statement, $flush = true): StatementId
    {
        $statementId = $this->statementRepository->storeStatement($statement);
        $this->objectManager->clear();

        return $statementId;
    }
}
