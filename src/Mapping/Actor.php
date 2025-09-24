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

use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\Actor as ActorModel;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\Group;
use Xabbuh\XApi\Model\InverseFunctionalIdentifier;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\IRL;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Actor
{
    public int $identifier;

    public string $type;

    public ?string $mbox = null;

    public ?string $mboxSha1Sum = null;

    public ?string $openId = null;

    public ?string $accountName = null;

    public ?string $accountHomePage = null;

    public ?string $name = null;

    /**
     * @var Actor[]
     */
    public mixed $members;

    public static function fromModel(ActorModel $actorModel): Actor
    {
        $inverseFunctionalIdentifier = $actorModel->getInverseFunctionalIdentifier();

        $actor = new self();
        $actor->mboxSha1Sum = $inverseFunctionalIdentifier?->getMboxSha1Sum();
        $actor->openId = $inverseFunctionalIdentifier?->getOpenId();
        $actor->name = $actorModel->getName();

        if (($mbox = $inverseFunctionalIdentifier?->getMbox()) instanceof IRI) {
            $actor->mbox = $mbox->getValue();
        }

        if (($account = $inverseFunctionalIdentifier?->getAccount()) instanceof Account) {
            $actor->accountName = $account->getName();
            $actor->accountHomePage = $account->getHomePage()->getValue();
        }

        if ($actorModel instanceof Group) {
            $actor->type = 'group';
            $actor->members = [];

            foreach ($actorModel->getMembers() as $agent) {
                $actor->members[] = self::fromModel($agent);
            }
        } else {
            $actor->type = 'agent';
        }

        return $actor;
    }

    public function getModel(): Agent|Group
    {
        $inverseFunctionalIdentifier = null;

        if (null !== $this->mbox) {
            $inverseFunctionalIdentifier = InverseFunctionalIdentifier::withMbox(IRI::fromString($this->mbox));
        } elseif (null !== $this->mboxSha1Sum) {
            $inverseFunctionalIdentifier = InverseFunctionalIdentifier::withMboxSha1Sum($this->mboxSha1Sum);
        } elseif (null !== $this->openId) {
            $inverseFunctionalIdentifier = InverseFunctionalIdentifier::withOpenId($this->openId);
        } elseif (null !== $this->accountName && null !== $this->accountHomePage) {
            $inverseFunctionalIdentifier = InverseFunctionalIdentifier::withAccount(new Account($this->accountName, IRL::fromString($this->accountHomePage)));
        }

        if ('group' === $this->type) {
            $members = [];

            foreach ($this->members as $member) {
                $members[] = $member->getModel();
            }

            return new Group($inverseFunctionalIdentifier, $this->name, $members);
        }

        return new Agent($inverseFunctionalIdentifier, $this->name);
    }
}
