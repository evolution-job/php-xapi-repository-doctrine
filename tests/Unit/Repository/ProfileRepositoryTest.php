<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Unit\Repository;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Model\ProfileDocument;
use XApi\Repository\Doctrine\Mapping\Profile;
use XApi\Repository\Doctrine\Repository\Mapping\ProfileRepository as MappedProfileRepository;
use XApi\Repository\Doctrine\Repository\ProfileRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final class ProfileRepositoryTest extends TestCase
{
    public function testItMapsStoredDocumentsToThePersistenceRepository(): void
    {
        $mappedRepository = $this->createMock(MappedProfileRepository::class);
        $document = new ProfileDocument('{}', 'application/json', new DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $mappedRepository->expects(self::once())->method('storeProfile')->with(self::callback(static fn (Profile $profile): bool => 'activity:https://example.org/activity' === $profile->resource && 'resume' === $profile->profileId && $document == $profile->getModel()));

        (new ProfileRepository($mappedRepository))->store('activity:https://example.org/activity', 'resume', $document);
    }

    public function testItMapsFoundPersistenceDocumentsToModels(): void
    {
        $mappedRepository = $this->createMock(MappedProfileRepository::class);
        $document = new ProfileDocument('{}', 'application/json', new DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $mappedRepository->expects($this->once())->method('findProfile')->with('activity:https://example.org/activity', 'resume')->willReturn(Profile::fromModel('activity:https://example.org/activity', 'resume', $document));

        self::assertEquals($document, (new ProfileRepository($mappedRepository))->find('activity:https://example.org/activity', 'resume'));
    }
}
