<?php

declare(strict_types=1);

namespace XApi\Repository\Doctrine\Repository\Mapping;

use DateTimeImmutable;
use XApi\Repository\Doctrine\Mapping\Profile;

interface ProfileRepository
{
    public function findProfile(string $resource, string $profileId): ?Profile;

    /** @return Profile[] */
    public function findProfiles(string $resource, ?DateTimeImmutable $since = null): array;

    public function removeProfile(string $resource, ?string $profileId = null, bool $flush = true): void;

    public function storeProfile(Profile $profile, bool $flush = true): void;
}
