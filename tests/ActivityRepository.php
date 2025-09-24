<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Tests;

use Doctrine\Persistence\ObjectManager;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\IRI;
use XApi\Repository\Api\ActivityRepositoryInterface;

/**
 * Activity repository clearing the object manager between read and write operations.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
final readonly class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(private ActivityRepositoryInterface $activityRepository, private ObjectManager $objectManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findActivityById(IRI $iri): Activity
    {
        $activity = $this->activityRepository->findActivityById($iri);
        $this->objectManager->clear();

        return $activity;
    }
}