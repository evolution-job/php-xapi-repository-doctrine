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
use Xabbuh\XApi\Model\IRI;
use XApi\Repository\Api\Tests\Functional\ActivityRepositoryTestCase as BaseActivityRepositoryTestCase;
use XApi\Repository\Doctrine\Repository\ActivityRepository;
use XApi\Repository\Doctrine\Repository\Mapping\StatementObjectRepository;
use XApi\Repository\Doctrine\Tests\ActivityRepository as FreshActivityRepository;

/**
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
abstract class ActivityRepositoryTestCase extends BaseActivityRepositoryTestCase
{
    protected ObjectManager $objectManager;

    protected ObjectRepository|StatementObjectRepository $storage;

    #[Override]
    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->storage = $this->createStorage();

        parent::setUp();
    }

    protected function createActivityRepository(): FreshActivityRepository
    {
        return new FreshActivityRepository(new ActivityRepository($this->storage), $this->objectManager);
    }

    protected function cleanDatabase(): void
    {
        $this->objectManager->remove($this->storage->findObject(['type' => 'activity', 'activityId' => IRI::fromString('http://tincanapi.com/conformancetest/activityid')->getValue(),]));

        $this->objectManager->flush();
    }

    abstract protected function createObjectManager();

    abstract protected function getActivityClassName(): string;

    private function createStorage(): ObjectRepository|StatementObjectRepository
    {
        return $this->objectManager->getRepository($this->getActivityClassName());
    }
}