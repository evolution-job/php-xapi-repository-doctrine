<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Functional;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use XApi\Repository\Api\ProfileRepositoryInterface;
use XApi\Repository\Api\Tests\Functional\ProfileRepositoryTestCase as BaseProfileRepositoryTestCase;
use XApi\Repository\Doctrine\Repository\Mapping\ProfileRepository as MappedProfileRepository;
use XApi\Repository\Doctrine\Repository\ProfileRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class ProfileRepositoryTestCase extends BaseProfileRepositoryTestCase
{
    protected ObjectManager $objectManager;

    protected ObjectRepository|MappedProfileRepository $repository;

    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->repository = $this->objectManager->getRepository($this->getProfileClassName());
        parent::setUp();
    }

    protected function createProfileRepository(): ProfileRepositoryInterface
    {
        return new ProfileRepository($this->repository);
    }

    protected function cleanDatabase(): void
    {
        foreach ($this->repository->findAll() as $profile) {
            $this->objectManager->remove($profile);
        }

        $this->objectManager->flush();
    }

    abstract protected function createObjectManager(): ObjectManager;

    abstract protected function getProfileClassName(): string;
}
