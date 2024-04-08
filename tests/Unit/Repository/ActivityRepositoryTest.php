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
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use Xabbuh\XApi\DataFixtures\ActorFixtures;
use Xabbuh\XApi\Model\IRI;
use XApi\Repository\Doctrine\Mapping\StatementObject;
use XApi\Repository\Doctrine\Repository\ActivityRepository;
use XApi\Repository\Doctrine\Storage\StatementObjectStorage;

/**
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
class ActivityRepositoryTest extends TestCase
{
    private MockObject|StatementObjectStorage $objectStorage;

    private ActivityRepository $activityRepository;

    protected function setUp(): void
    {
        $this->objectStorage = $this->createObjectStorageMock();
        $this->activityRepository = new ActivityRepository($this->objectStorage);
    }

    public function testFindActivityById(): void
    {
        $iri = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(['type' => 'activity', 'activityId' => $iri->getValue()])
            ->willReturn(StatementObject::fromModel(ActivityFixtures::getIdActivity()));

        $this->activityRepository->findActivityById($iri);
    }

    public function testNotFoundObject(): void
    {
        $this->expectException(NotFoundException::class);
        $iri = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(['type' => 'activity', 'activityId' => $iri->getValue()])
            ->willReturn(null);

        $this->activityRepository->findActivityById($iri);
    }

    public function testObjectIsNotAnActivity(): void
    {
        $this->expectException(NotFoundException::class);
        $iri = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(['type' => 'activity', 'activityId' => $iri->getValue()])
            ->willReturn(StatementObject::fromModel(ActorFixtures::getMboxAgent()));

        $this->activityRepository->findActivityById($iri);
    }

    protected function createObjectStorageMock(): MockObject
    {
        return $this
            ->getMockBuilder(StatementObjectStorage::class)
            ->getMock();
    }
}