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
use XApi\Repository\Api\Tests\Functional\StateRepositoryTestCase as BaseStateRepositoryTestCase;
use XApi\Repository\Doctrine\Repository\Mapping\StateRepository as MappedStateRepository;
use XApi\Repository\Doctrine\Repository\StateRepository;
use XApi\Repository\Doctrine\Tests\StateRepository as FreshStateRepository;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StateRepositoryTestCase extends BaseStateRepositoryTestCase
{
    protected ObjectManager $objectManager;

    protected ObjectRepository|MappedStateRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->repository = $this->createRepository();

        parent::setUp();
    }

    protected function createStateRepository(): FreshStateRepository
    {
        return new FreshStateRepository(new StateRepository($this->repository), $this->objectManager);
    }

    protected function cleanDatabase(): void
    {
        foreach ($this->repository->findAll() as $state) {
            $this->objectManager->remove($state);
        }

        $this->objectManager->flush();
    }

    abstract protected function createObjectManager(): ObjectManager;

    abstract protected function getStateClassName(): string;

    private function createRepository(): ObjectRepository|MappedStateRepository
    {
        return $this->objectManager->getRepository($this->getStateClassName());
    }
}
