<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Functional;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Override;
use XApi\Repository\Api\Tests\Functional\StatementRepositoryTestCase as BaseStatementRepositoryTestCase;
use XApi\Repository\Doctrine\Repository\Mapping\StatementRepository as MappedStatementRepository;
use XApi\Repository\Doctrine\Repository\StatementRepository;
use XApi\Repository\Doctrine\Tests\StatementRepository as FreshStatementRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StatementRepositoryTestCase extends BaseStatementRepositoryTestCase
{
    protected ObjectManager $objectManager;

    protected ObjectRepository|MappedStatementRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->repository = $this->createRepository();

        parent::setUp();
    }

    protected function createStatementRepository(): FreshStatementRepository
    {
        return new FreshStatementRepository(new StatementRepository($this->repository), $this->objectManager);
    }

    protected function cleanDatabase(): void
    {
        foreach ($this->repository->findStatements([]) as $statement) {
            $this->objectManager->remove($statement);
        }

        $this->objectManager->flush();
    }

    abstract protected function createObjectManager(): ObjectManager;

    abstract protected function getStatementClassName(): string;

    private function createRepository(): ObjectRepository|MappedStatementRepository
    {
        return $this->objectManager->getRepository($this->getStatementClassName());
    }
}
