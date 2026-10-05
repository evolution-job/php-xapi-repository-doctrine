<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Unit\Repository\Mapping;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Model\ProfileDocument;
use XApi\Repository\Doctrine\Mapping\Profile;
use XApi\Repository\Doctrine\Repository\Mapping\ProfileRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class ProfileRepositoryTestCase extends TestCase
{
    private MockObject $objectManager;

    private ProfileRepository $profileRepository;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock($this->getObjectManagerClass());
        $this->profileRepository = $this->createMappedProfileRepository(
            $this->objectManager,
            $this->createMock($this->getUnitOfWorkClass()),
            $this->createMock($this->getClassMetadataClass())
        );
    }

    public function testProfileIsPersistedAndFlushed(): void
    {
        $profile = Profile::fromModel('activity:https://example.org/activity', 'resume', new ProfileDocument('{}', 'application/json', new DateTimeImmutable()));
        $this->objectManager->expects(self::once())->method('persist')->with(self::isInstanceOf(Profile::class));
        $this->objectManager->expects(self::once())->method('flush');

        $this->profileRepository->storeProfile($profile);
    }

    abstract protected function getObjectManagerClass(): string;

    abstract protected function getUnitOfWorkClass(): string;

    abstract protected function getClassMetadataClass(): string;

    abstract protected function createMappedProfileRepository(object $objectManager, object $unitOfWork, object $classMetadata): ProfileRepository;
}
