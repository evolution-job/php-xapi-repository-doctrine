<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace XApi\Repository\Doctrine\Repository;

use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\Verb;
use XApi\Repository\Api\VerbRepositoryInterface;
use XApi\Repository\Doctrine\Repository\Mapping\VerbRepository as BaseVerbRepository;

/**
 * Doctrine based {@link Verb} repository.
 */
final readonly class VerbRepository implements VerbRepositoryInterface
{
    public function __construct(private BaseVerbRepository $baseVerbRepository) { }

    public function findVerbById(IRI $iri): Verb
    {
        $verb = $this->baseVerbRepository->findVerb(['id' => $iri->getValue()]);
        if (!$verb instanceof \XApi\Repository\Doctrine\Mapping\Verb) {
            throw new NotFoundException(sprintf('No verb could be found matching the ID "%s".', $iri->getValue()));
        }

        return $verb->getModel();
    }
}
