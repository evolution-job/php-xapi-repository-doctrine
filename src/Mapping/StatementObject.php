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

use InvalidArgumentException;
use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\Actor as ActorModel;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\Definition;
use Xabbuh\XApi\Model\Group;
use Xabbuh\XApi\Model\InverseFunctionalIdentifier;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\IRL;
use Xabbuh\XApi\Model\LanguageMap;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementObject as StatementObjectModel;
use Xabbuh\XApi\Model\StatementReference;
use Xabbuh\XApi\Model\SubStatement;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementObject
{
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_AGENT = 'agent';
    public const TYPE_GROUP = 'group';
    public const TYPE_STATEMENT_REFERENCE = 'statement_reference';
    public const TYPE_SUB_STATEMENT = 'sub_statement';

    public int $identifier;

    public ?string $type = null;

    public ?string $activityId = null;

    public ?bool $hasActivityDefinition = null;

    public ?bool $hasActivityName = null;

    public ?array $activityName = null;

    public ?bool $hasActivityDescription = null;

    public ?array $activityDescription = null;

    public ?string $activityType = null;

    public ?string $activityMoreInfo = null;

    public ?Extensions $activityExtensions = null;

    public ?string $mbox = null;

    public ?string $mboxSha1Sum = null;

    public ?string $openId = null;

    public ?string $accountName = null;

    public ?string $accountHomePage = null;

    public ?string $name = null;

    /**
     * @var StatementObject[]
     */
    public $members;

    public ?StatementObject $group = null;

    public ?string $referencedStatementId = null;

    public ?StatementObject $actor = null;

    public ?Verb $verb = null;

    public ?StatementObject $object = null;

    public ?Result $result = null;

    public ?Context $context = null;

    public ?Context $parentContext = null;

    public ?Context $groupingContext = null;

    public ?Context $categoryContext = null;

    public ?Context $otherContext = null;

    public static function fromModel($model): ?StatementObject
    {
        if (!$model instanceof StatementObjectModel) {
            throw new InvalidArgumentException(sprintf('Expected a statement object but got %s', get_debug_type($model)));
        }

        if ($model instanceof ActorModel) {
            return self::fromActor($model);
        }

        if ($model instanceof StatementReference) {
            $object = new self();
            $object->type = self::TYPE_STATEMENT_REFERENCE;
            $object->referencedStatementId = $model->getStatementId()->getValue();

            return $object;
        }

        if ($model instanceof Activity) {
            return self::fromActivity($model);
        }

        if ($model instanceof SubStatement) {
            return self::fromSubStatement($model);
        }

        return null;
    }

    public function getModel(): StatementReference|Agent|Activity|Group|SubStatement
    {
        if (self::TYPE_AGENT === $this->type || self::TYPE_GROUP === $this->type) {
            return $this->getActorModel();
        }

        if (self::TYPE_STATEMENT_REFERENCE === $this->type) {
            return new StatementReference(StatementId::fromString($this->referencedStatementId));
        }

        if (self::TYPE_SUB_STATEMENT === $this->type) {
            return $this->getSubStatementModel();
        }

        return $this->getActivityModel();
    }

    private static function fromActivity(Activity $activity): self
    {
        $object = new self();
        $object->activityId = $activity->getId()->getValue();

        if (($definition = $activity->getDefinition()) instanceof Definition) {
            $object->hasActivityDefinition = true;

            if (($name = $definition->getName()) instanceof LanguageMap) {
                $object->hasActivityName = true;
                $object->activityName = [];

                foreach ($name->languageTags() as $languageTag) {
                    $object->activityName[$languageTag] = $name[$languageTag];
                }
            } else {
                $object->hasActivityName = false;
            }

            if (($description = $definition->getDescription()) instanceof LanguageMap) {
                $object->hasActivityDescription = true;
                $object->activityDescription = [];

                foreach ($description->languageTags() as $languageTag) {
                    $object->activityDescription[$languageTag] = $description[$languageTag];
                }
            } else {
                $object->hasActivityDescription = false;
            }

            if (($type = $definition->getType()) instanceof IRI) {
                $object->activityType = $type->getValue();
            }

            if (($moreInfo = $definition->getMoreInfo()) instanceof IRL) {
                $object->activityMoreInfo = $moreInfo->getValue();
            }

            if (($extensions = $definition->getExtensions()) instanceof \Xabbuh\XApi\Model\Extensions) {
                $object->activityExtensions = Extensions::fromModel($extensions);
            }
        } else {
            $object->hasActivityDefinition = false;
        }

        return $object;
    }

    private static function fromActor(ActorModel $actorModel): self
    {
        $inverseFunctionalIdentifier = $actorModel->getInverseFunctionalIdentifier();

        $object = new self();
        $object->mboxSha1Sum = $inverseFunctionalIdentifier?->getMboxSha1Sum();
        $object->openId = $inverseFunctionalIdentifier?->getOpenId();
        $object->name = $actorModel->getName();

        if (($mbox = $inverseFunctionalIdentifier?->getMbox()) instanceof IRI) {
            $object->mbox = $mbox->getValue();
        }

        if (($account = $inverseFunctionalIdentifier?->getAccount()) instanceof Account) {
            $object->accountName = $account->getName();
            $object->accountHomePage = $account->getHomePage()->getValue();
        }

        if ($actorModel instanceof Group) {
            $object->type = self::TYPE_GROUP;
            $object->members = [];

            foreach ($actorModel->getMembers() as $member) {
                $object->members[] = self::fromActor($member);
            }
        } else {
            $object->type = self::TYPE_AGENT;
        }

        return $object;
    }

    private static function fromSubStatement(SubStatement $subStatement): self
    {
        $object = new self();
        $object->type = self::TYPE_SUB_STATEMENT;
        $object->actor = self::fromModel($subStatement->getActor());
        $object->verb = Verb::fromModel($subStatement->getVerb());
        $object->object = self::fromModel($subStatement->getObject());

        return $object;
    }

    private function getActivityModel(): Activity
    {
        $definition = null;
        $type = null;
        $moreInfo = null;

        if ($this->hasActivityDefinition) {
            $name = null;
            $description = null;

            if ($this->hasActivityName) {
                $name = LanguageMap::create($this->activityName);
            }

            if ($this->hasActivityDescription) {
                $description = LanguageMap::create($this->activityDescription);
            }

            if (null !== $this->activityType) {
                $type = IRI::fromString($this->activityType);
            }

            if (null !== $this->activityMoreInfo) {
                $moreInfo = IRL::fromString($this->activityMoreInfo);
            }

            $extensions = $this->activityExtensions?->getModel();

            $definition = new Definition($name, $description, $type, $moreInfo, $extensions);
        }

        return new Activity(IRI::fromString($this->activityId), $definition);
    }

    private function getActorModel(): Group|Agent
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

        if (self::TYPE_GROUP === $this->type) {
            $members = [];

            foreach ($this->members as $member) {
                $members[] = $member->getModel();
            }

            return new Group($inverseFunctionalIdentifier, $this->name, $members);
        }

        return new Agent($inverseFunctionalIdentifier, $this->name);
    }

    private function getSubStatementModel(): SubStatement
    {
        $result = null;
        $context = null;

        return new SubStatement(
            $this->actor->getModel(),
            $this->verb->getModel(),
            $this->object->getModel(),
            $result,
            $context
        );
    }
}
