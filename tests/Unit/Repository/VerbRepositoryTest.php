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
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\DataFixtures\VerbFixtures;
use XApi\Repository\Doctrine\Mapping\Verb;
use XApi\Repository\Doctrine\Repository\Mapping\VerbRepository as MappedVerbRepository;
use XApi\Repository\Doctrine\Repository\VerbRepository;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final class VerbRepositoryTest extends TestCase
{
    public function testItReturnsTheMappedVerbModel(): void
    {
        $verb = VerbFixtures::getTypicalVerb();
        $repository = $this->createMock(MappedVerbRepository::class);
        $repository->expects(self::once())->method('findVerb')->with(['id' => $verb->getId()->getValue()])->willReturn(Verb::fromModel($verb));

        self::assertTrue($verb->equals((new VerbRepository($repository))->findVerbById($verb->getId())));
    }

    public function testItThrowsWhenTheVerbDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);
        $repository = $this->createMock(MappedVerbRepository::class);
        $repository->method('findVerb')->willReturn(null);

        (new VerbRepository($repository))->findVerbById(VerbFixtures::getTypicalVerb()->getId());
    }
}
