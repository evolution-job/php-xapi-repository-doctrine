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
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use XApi\Repository\Doctrine\Mapping\StatementObject;
use XApi\Repository\Doctrine\Repository\Mapping\StatementObjectRepository as MappedStatementObjectRepository;

abstract class StatementObjectRepositoryTestCase extends TestCase
{
    protected ObjectManager $objectManager;
    protected ObjectRepository|MappedStatementObjectRepository $repository;

    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->repository = $this->createMappedStatementObjectRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void { $this->cleanDatabase(); }

    public function testItReturnsNullForAnUnknownObject(): void
    {
        self::assertNull($this->repository->findObject(['type' => StatementObject::TYPE_ACTIVITY, 'activityId' => 'https://example.org/unknown']));
    }

    public function testItFindsTheFirstMatchingObject(): void
    {
        $object = StatementObject::fromModel(ActivityFixtures::getTypicalActivity());
        $this->objectManager->persist($object);
        $this->objectManager->flush();

        self::assertSame($object->identifier, $this->repository->findObject(['type' => StatementObject::TYPE_ACTIVITY, 'activityId' => $object->activityId])->identifier);
    }

    protected function cleanDatabase(): void
    {
        foreach ($this->repository->findAll() as $object) { $this->objectManager->remove($object); }
        $this->objectManager->flush();
    }

    abstract protected function createObjectManager(): ObjectManager;
    abstract protected function getStatementObjectClassName(): string;
    abstract protected function createMappedStatementObjectRepository(): ObjectRepository|MappedStatementObjectRepository;
}
