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

    public StatementObject $agent;

    public ?string $registrationId = null;

    public string $stateId;

    public mixed $data;

    public static function fromModel(StateModel $stateModel): self
    {
        $state = new self();

        $state->activityId = $stateModel->getActivity()->getId()->getValue();
        $state->agent = StatementObject::fromModel($stateModel->getAgent());
        $state->registrationId = $stateModel->getRegistrationId();
        $state->stateId = $stateModel->getStateId();

        try {
            $state->data = is_array($stateModel->getData()) ? json_encode($stateModel->getData(), JSON_THROW_ON_ERROR) : $stateModel->getData();
        } catch (JsonException) {
        }

        return $state;
    }

    public function getModel(): StateModel
    {
        try {
            $data = json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $data = $this->data;
        }

        return new StateModel(
            new Activity(IRI::fromString($this->activityId)),
            $this->agent->getModel(),
            $this->stateId,
            $this->registrationId,
            $data
        );
    }
}
