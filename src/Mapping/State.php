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

use JsonException;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\State as StateModel;

class State
{
    public string $activityId;

    public StatementObject $actor;

    public ?string $registrationId = null;

    public string $stateId;

    public string $data;

    public static function fromModel(StateModel $stateModel): self
    {
        $state = new self();

        $state->activityId = $stateModel->getActivity()->getId()->getValue();
        $state->actor = StatementObject::fromModel($stateModel->getAgent());
        $state->registrationId = $stateModel->getRegistrationId();
        $state->stateId = $stateModel->getStateId();

        try {
            $state->data = is_array($stateModel->getData()) ? json_encode($stateModel->getData(), JSON_THROW_ON_ERROR) : $stateModel->getData();
        } catch (JsonException) { }

        return $state;
    }

    public function getModel(): StateModel
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
