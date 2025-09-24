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

/**
 * Doctrine based {@link Activity} repository.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
final class ActivityRepository extends StatementObjectRepository implements ActivityRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActivityById(IRI $iri): ?Activity
    {
        $criteria = ['type' => StatementObject::TYPE_ACTIVITY, 'activityId' => $iri->getValue()];

        $statementObject = $this->statementObjectRepository->findObject($criteria);

        if (!$statementObject instanceof StatementObject) {
            throw new NotFoundException(sprintf('No activity could be found matching the ID "%s".', $iri->getValue()));
        }

        $model = $statementObject->getModel();

        if (!$model instanceof Activity) {
            throw new NotFoundException(sprintf('No activity could be found matching the ID "%s".', $iri->getValue()));
        }

        return $model;
    }
}