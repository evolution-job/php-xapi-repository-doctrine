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

use DateTime;
use DateTimeZone;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Statement as StatementModel;
use Xabbuh\XApi\Model\StatementId;

/**
 * A {@link Statement} mapped to a storage backend.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Statement
{
    public string $id;

    public ?StatementObject $actor = null;

    public ?Verb $verb = null;

    public ?StatementObject $object = null;

    public ?Result $result = null;

    public ?StatementObject $authority = null;

    public ?DateTime $created = null;

    public ?DateTime $stored = null;

    public ?Context $context = null;

    public bool $hasAttachments;

    /**
     * @var Attachment[]
     */
    public mixed $attachments;

    public ?string $version = null;

    public static function fromModel(StatementModel $statementModel): self
    {
        $statement = new self();
        $statement->id = $statementModel->getId()?->getValue();
        $statement->actor = StatementObject::fromModel($statementModel->getActor());
        $statement->verb = Verb::fromModel($statementModel->getVerb());
        $statement->object = StatementObject::fromModel($statementModel->getObject());
        $statement->version = $statementModel->getVersion();

        if ($statementModel->getCreated() instanceof DateTime) {
            $statementModel->getCreated()->setTimezone(new DateTimeZone('UTC'));
            $statement->created = $statementModel->getCreated();
        }

        if (($result = $statementModel->getResult()) instanceof \Xabbuh\XApi\Model\Result) {
            $statement->result = Result::fromModel($result);
        }

        if (($authority = $statementModel->getAuthority()) instanceof Actor) {
            $statement->authority = StatementObject::fromModel($authority);
        }

        if (($context = $statementModel->getContext()) instanceof \Xabbuh\XApi\Model\Context) {
            $statement->context = Context::fromModel($context);
        }

        if (null !== $attachments = $statementModel->getAttachments()) {
            $statement->hasAttachments = true;
            $statement->attachments = [];

            foreach ($attachments as $attachment) {
                $mappedAttachment = Attachment::fromModel($attachment);
                $mappedAttachment->statement = $statement;
                $statement->attachments[] = $mappedAttachment;
            }
        } else {
            $statement->hasAttachments = false;
        }

        return $statement;
    }

    public function getModel(): StatementModel
    {
        $attachments = null;
        $authority = $this->authority?->getModel();
        $created = null;
        $result = $this->result?->getModel();
        $stored = null;

        if ($this->created instanceof DateTime) {
            $created = $this->created;
        }

        if ($this->stored instanceof DateTime) {
            $stored = $this->stored;
        }

        $context = $this->context?->getModel();

        if ($this->hasAttachments) {
            $attachments = [];

            foreach ($this->attachments as $attachment) {
                $attachments[] = $attachment->getModel();
            }
        }

        return new StatementModel(
            StatementId::fromString($this->id),
            $this->actor->getModel(),
            $this->verb->getModel(),
            $this->object->getModel(),
            $result,
            $authority,
            $created,
            $stored,
            $context,
            $attachments,
            $this->version
        );
    }
}
