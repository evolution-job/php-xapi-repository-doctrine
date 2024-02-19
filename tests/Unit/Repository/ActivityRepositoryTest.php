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

use PHPUnit\Framework\TestCase;
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
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StatementObjectStorage
     */
    private $objectStorage;

    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    protected function setUp()
    {
        $this->objectStorage = $this->createObjectStorageMock();
        $this->activityRepository = new ActivityRepository($this->objectStorage);
    }

    public function testFindActivityById()
    {
        $activityId = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(array(
                'type' => 'activity',
                'activityId' => $activityId->getValue(),
            ))
            ->will($this->returnValue(StatementObject::fromModel(ActivityFixtures::getIdActivity())));

        $this->activityRepository->findActivityById($activityId);
    }

    /**
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testNotFoundObject()
    {
        $activityId = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(array(
                'type' => 'activity',
                'activityId' => $activityId->getValue(),
            ))
            ->will($this->returnValue(null));

        $this->activityRepository->findActivityById($activityId);
    }

    /**
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testObjectIsNotAnActivity()
    {
        $activityId = IRI::fromString('http://tincanapi.com/conformancetest/activityid');

        $this
            ->objectStorage
            ->expects($this->once())
            ->method('findObject')
            ->with(array(
                'type' => 'activity',
                'activityId' => $activityId->getValue(),
            ))
            ->will($this->returnValue(StatementObject::fromModel(ActorFixtures::getMboxAgent())));

        $this->activityRepository->findActivityById($activityId);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StatementObjectStorage
     */
    protected function createObjectStorageMock()
    {
        return $this
            ->getMockBuilder('\XApi\Repository\Doctrine\Storage\StatementObjectStorage')
            ->getMock();
    }
}