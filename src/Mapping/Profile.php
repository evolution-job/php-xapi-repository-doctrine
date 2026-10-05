<?php

declare(strict_types=1);

namespace XApi\Repository\Doctrine\Mapping;

use DateTimeImmutable;
use Xabbuh\XApi\Model\ProfileDocument;

class Profile
{
    public string $resource;

    public string $profileId;

    public string $content;

    public string $contentType;

    public DateTimeImmutable $updated;

    public static function fromModel(string $resource, string $profileId, ProfileDocument $profileDocument): self
    {
        $profile = new self();
        $profile->resource = $resource;
        $profile->profileId = $profileId;
        $profile->content = $profileDocument->content;
        $profile->contentType = $profileDocument->contentType;
        $profile->updated = $profileDocument->updated;

        return $profile;
    }

    public function getModel(): ProfileDocument
    {
        return new ProfileDocument($this->content, $this->contentType, $this->updated);
    }
}
