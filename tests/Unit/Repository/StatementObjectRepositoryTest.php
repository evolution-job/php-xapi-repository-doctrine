<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use XApi\Repository\Doctrine\Mapping\StatementObject;
use XApi\Repository\Doctrine\Repository\Mapping\StatementObjectRepository as MappedStatementObjectRepository;
use XApi\Repository\Doctrine\Repository\StatementObjectRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final class StatementObjectRepositoryTest extends TestCase
{
    public function testItDelegatesObjectLookupToThePersistenceRepository(): void
    {
        $criteria = ['type' => StatementObject::TYPE_ACTIVITY];
        $object = StatementObject::fromModel(ActivityFixtures::getTypicalActivity());
        $mappedRepository = $this->createMock(MappedStatementObjectRepository::class);
        $mappedRepository->expects(self::once())->method('findObject')->with($criteria)->willReturn($object);
        $repository = new class($mappedRepository) extends StatementObjectRepository {};

        self::assertSame($object, $repository->findObject($criteria));
    }
}
