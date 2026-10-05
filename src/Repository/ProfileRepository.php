<?php

declare(strict_types=1);

namespace XApi\Repository\Doctrine\Repository;

use DateTimeImmutable;
use Xabbuh\XApi\Model\ProfileDocument;
use XApi\Repository\Api\ProfileRepositoryInterface;
use XApi\Repository\Doctrine\Mapping\Profile as MappedProfile;
use XApi\Repository\Doctrine\Repository\Mapping\ProfileRepository as BaseProfileRepository;

final readonly class ProfileRepository implements ProfileRepositoryInterface
{
    public function __construct(private BaseProfileRepository $baseProfileRepository) { }

    public function find(string $resource, string $profileId): ?ProfileDocument
    {
        return $this->baseProfileRepository->findProfile($resource, $profileId)?->getModel();
    }

    public function findIds(string $resource, ?DateTimeImmutable $since = null): array
    {
        return array_map(
            static fn (MappedProfile $profile): string => $profile->profileId,
            $this->baseProfileRepository->findProfiles($resource, $since)
        );
    }

    public function store(string $resource, string $profileId, ProfileDocument $profileDocument): void
    {
        $this->baseProfileRepository->storeProfile(MappedProfile::fromModel($resource, $profileId, $profileDocument));
    }

    public function remove(string $resource, ?string $profileId = null): void
    {
        $this->baseProfileRepository->removeProfile($resource, $profileId);
    }
}
