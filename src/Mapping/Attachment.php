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

use Xabbuh\XApi\Model\Attachment as AttachmentModel;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\IRL;
use Xabbuh\XApi\Model\LanguageMap;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Attachment
{
    public int $identifier;

    public ?Statement $statement = null;

    public string $usageType;

    public string $contentType;

    public int $length;

    public string $sha2;

    public array $display = [];

    public bool $hasDescription;

    public ?array $description = null;

    public ?string $fileUrl = null;

    public ?string $content = null;

    public static function fromModel(AttachmentModel $attachmentModel): self
    {
        $attachment = new self();
        $attachment->usageType = $attachmentModel->getUsageType()->getValue();
        $attachment->contentType = $attachmentModel->getContentType();
        $attachment->length = $attachmentModel->getLength();
        $attachment->sha2 = $attachmentModel->getSha2();
        $attachment->display = [];

        if ($attachmentModel->getFileUrl() instanceof IRL) {
            $attachment->fileUrl = $attachmentModel->getFileUrl()->getValue();
        }

        $attachment->content = $attachmentModel->getContent();

        $display = $attachmentModel->getDisplay();

        foreach ($display->languageTags() as $languageTag) {
            $attachment->display[$languageTag] = $display[$languageTag];
        }

        if (($description = $attachmentModel->getDescription()) instanceof LanguageMap) {
            $attachment->hasDescription = true;
            $attachment->description = [];

            foreach ($description->languageTags() as $languageTag) {
                $attachment->description[$languageTag] = $description[$languageTag];
            }
        } else {
            $attachment->hasDescription = false;
        }

        return $attachment;
    }

    public function getModel(): AttachmentModel
    {
        $description = null;
        $fileUrl = null;

        if ($this->hasDescription) {
            $description = LanguageMap::create($this->description);
        }

        if (null !== $this->fileUrl) {
            $fileUrl = IRL::fromString($this->fileUrl);
        }

        return new AttachmentModel(IRI::fromString($this->usageType), $this->contentType, $this->length, $this->sha2, LanguageMap::create($this->display), $description, $fileUrl, $this->content);
    }
}
