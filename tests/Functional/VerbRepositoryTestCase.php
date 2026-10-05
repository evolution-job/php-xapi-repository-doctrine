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
use Xabbuh\XApi\DataFixtures\VerbFixtures;
use Xabbuh\XApi\Model\Verb;
use XApi\Repository\Api\Tests\Functional\VerbRepositoryTestCase as BaseVerbRepositoryTestCase;
use XApi\Repository\Api\VerbRepositoryInterface;
use XApi\Repository\Doctrine\Mapping\Verb as MappedVerb;
use XApi\Repository\Doctrine\Repository\Mapping\VerbRepository as MappedVerbRepository;
use XApi\Repository\Doctrine\Repository\VerbRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class VerbRepositoryTestCase extends BaseVerbRepositoryTestCase
{
    protected ObjectManager $objectManager;
    protected ObjectRepository|MappedVerbRepository $repository;

    protected function setUp(): void
    {
        $this->objectManager = $this->createObjectManager();
        $this->repository = $this->createMappedVerbRepository();
        parent::setUp();
    }

    protected function createVerbRepository(): VerbRepositoryInterface { return new VerbRepository($this->repository); }

    protected function givenKnownVerb(): Verb
    {
        $verb = VerbFixtures::getTypicalVerb();
        $this->objectManager->persist(MappedVerb::fromModel($verb));
        $this->objectManager->flush();

        return $verb;
    }

    protected function cleanDatabase(): void
    {
        foreach ($this->repository->findAll() as $verb) { $this->objectManager->remove($verb); }
        $this->objectManager->flush();
    }

    abstract protected function createObjectManager(): ObjectManager;
    abstract protected function getVerbClassName(): string;
    abstract protected function createMappedVerbRepository(): ObjectRepository|MappedVerbRepository;
}
