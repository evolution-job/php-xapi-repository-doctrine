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

use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Context as ContextModel;
use Xabbuh\XApi\Model\ContextActivities;
use Xabbuh\XApi\Model\Group;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementReference;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Context
{
    public int $identifier;

    public ?string $registration = null;

    public ?StatementObject $instructor = null;

    public ?StatementObject $team = null;

    public ?bool $hasContextActivities = null;

    /**
     * @var StatementObject[]
     */
    public $parentActivities;

    /**
     * @var StatementObject[]
     */
    public $groupingActivities;

    /**
     * @var StatementObject[]
     */
    public $categoryActivities;

    /**
     * @var StatementObject[]
     */
    public $otherActivities;

    public ?string $revision = null;

    public ?string $platform = null;

    public ?string $language = null;

    public ?string $statement = null;

    public ?Extensions $extensions = null;

    public static function fromModel(ContextModel $contextModel): self
    {
        $context = new self();
        $context->registration = $contextModel->getRegistration();
        $context->revision = $contextModel->getRevision();
        $context->platform = $contextModel->getPlatform();
        $context->language = $contextModel->getLanguage();

        if (($instructor = $contextModel->getInstructor()) instanceof Actor) {
            $context->instructor = StatementObject::fromModel($instructor);
        }

        if (($team = $contextModel->getTeam()) instanceof Group) {
            $context->team = StatementObject::fromModel($team);
        }

        if (($contextActivities = $contextModel->getContextActivities()) instanceof ContextActivities) {
            $context->hasContextActivities = true;

            if (null !== $parentActivities = $contextActivities->getParentActivities()) {
                $context->parentActivities = [];

                foreach ($parentActivities as $parentActivity) {
                    $activity = StatementObject::fromModel($parentActivity);
                    $activity->parentContext = $context;
                    $context->parentActivities[] = $activity;
                }
            }

            if (null !== $groupingActivities = $contextActivities->getGroupingActivities()) {
                $context->groupingActivities = [];

                foreach ($groupingActivities as $groupingActivity) {
                    $activity = StatementObject::fromModel($groupingActivity);
                    $activity->groupingContext = $context;
                    $context->groupingActivities[] = $activity;
                }
            }

            if (null !== $categoryActivities = $contextActivities->getCategoryActivities()) {
                $context->categoryActivities = [];

                foreach ($categoryActivities as $categoryActivity) {
                    $activity = StatementObject::fromModel($categoryActivity);
                    $activity->categoryContext = $context;
                    $context->categoryActivities[] = $activity;
                }
            }

            if (null !== $otherActivities = $contextActivities->getOtherActivities()) {
                $context->otherActivities = [];

                foreach ($otherActivities as $otherActivity) {
                    $activity = StatementObject::fromModel($otherActivity);
                    $activity->otherContext = $context;
                    $context->otherActivities[] = $activity;
                }
            }
        } else {
            $context->hasContextActivities = false;
        }

        if (($statementReference = $contextModel->getStatement()) instanceof StatementReference) {
            $context->statement = $statementReference->getStatementId()->getValue();
        }

        if (($contextExtensions = $contextModel->getExtensions()) instanceof \Xabbuh\XApi\Model\Extensions) {
            $context->extensions = Extensions::fromModel($contextExtensions);
        }

        return $context;
    }

    public function getModel(): ContextModel
    {
        $context = new ContextModel();

        if (null !== $this->registration) {
            $context = $context->withRegistration($this->registration);
        }

        if (null !== $this->revision) {
            $context = $context->withRevision($this->revision);
        }

        if (null !== $this->platform) {
            $context = $context->withPlatform($this->platform);
        }

        if (null !== $this->language) {
            $context = $context->withLanguage($this->language);
        }

        if (null !== $this->instructor) {
            $context = $context->withInstructor($this->instructor->getModel());
        }

        if (null !== $this->team) {
            $context = $context->withTeam($this->team->getModel());
        }

        if ($this->hasContextActivities) {
            $contextActivities = new ContextActivities();

            if (null !== $this->parentActivities) {
                foreach ($this->parentActivities as $parentActivity) {
                    $contextActivities = $contextActivities->withAddedParentActivity($parentActivity->getModel());
                }
            }

            if (null !== $this->groupingActivities) {
                foreach ($this->groupingActivities as $groupingActivity) {
                    $contextActivities = $contextActivities->withAddedGroupingActivity($groupingActivity->getModel());
                }
            }

            if (null !== $this->categoryActivities) {
                foreach ($this->categoryActivities as $categoryActivity) {
                    $contextActivities = $contextActivities->withAddedCategoryActivity($categoryActivity->getModel());
                }
            }

            if (null !== $this->otherActivities) {
                foreach ($this->otherActivities as $otherActivity) {
                    $contextActivities = $contextActivities->withAddedOtherActivity($otherActivity->getModel());
                }
            }

            $context = $context->withContextActivities($contextActivities);
        }

        if (null !== $this->statement) {
            $context = $context->withStatement(new StatementReference(StatementId::fromString($this->statement)));
        }

        if (null !== $this->extensions) {
            $context = $context->withExtensions($this->extensions->getModel());
        }

        return $context;
    }
}
