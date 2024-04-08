<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Repository;

use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\IRI;
use XApi\Repository\Api\ActivityRepositoryInterface;
use XApi\Repository\Doctrine\Mapping\StatementObject;
use XApi\Repository\Doctrine\Storage\StatementObjectStorage;

/**
 * Doctrine based {@link Activity} repository.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
final class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(private readonly StatementObjectStorage $statementObjectStorage) { }

    /**
     * {@inheritdoc}
     */
    public function findActivityById(IRI $iri): Activity
    {
        $criteria = ['type' => StatementObject::TYPE_ACTIVITY, 'activityId' => $iri->getValue()];

        $mappedObject = $this->statementObjectStorage->findObject($criteria);

        if (null === $mappedObject) {
            throw new NotFoundException(sprintf('No activity could be found matching the ID "%s".', $iri->getValue()));
        }

        $activity = $mappedObject->getModel();

        if (!$activity instanceof Activity) {
            throw new NotFoundException(sprintf('No activity could be found matching the ID "%s".', $iri->getValue()));
        }

        return $activity;
    }
}