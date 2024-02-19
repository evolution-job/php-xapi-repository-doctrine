<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Mapping;

use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\State as StateModel;

class State
{
    /**
     * @var string
     */
    public $activityId;

    /**
     * @var StatementObject
     */
    public $actor;

    /**
     * @var string|null
     */
    public $registrationId;

    /**
     * @var string
     */
    public $stateId;

    /**
     * @var string
     */
    public $data;

    /**
     * @param StateModel $model
     * @return State
     */
    public static function fromModel(StateModel $model)
    {
        $state = new self();
        $state->activityId = $model->getActivity()->getId()->getValue();
        $state->actor = StatementObject::fromModel($model->getAgent());
        $state->registrationId = $model->getRegistrationId();
        $state->stateId = $model->getStateId();
        $state->data = is_array($model->getData()) ? json_encode($model->getData()) : $model->getData();
        return $state;
    }

    /**
     * @return StateModel
     */
    public function getModel()
    {
        return new StateModel(
            new Activity(IRI::fromString($this->activityId)),
            $this->actor->getModel(),
            $this->stateId,
            $this->registrationId,
            $this->data
        );
    }
}
