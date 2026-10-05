<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace XApi\Repository\Doctrine\Repository;

use XApi\Repository\Doctrine\Mapping\StatementObject;
use XApi\Repository\Doctrine\Repository\Mapping\StatementObjectRepository as BaseStatementObjectRepository;

/**
 * Doctrine based {@link Statement} repository.
 *
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class StatementObjectRepository implements BaseStatementObjectRepository
{
    public function __construct(protected readonly BaseStatementObjectRepository $statementObjectRepository) { }

    /**
     * {@inheritdoc}
     */
    public function findObject(array $criteria): ?StatementObject
    {
        return $this->statementObjectRepository->findObject($criteria);
    }
}
