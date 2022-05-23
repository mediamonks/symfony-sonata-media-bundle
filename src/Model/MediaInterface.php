<?php

namespace MediaMonks\SonataMediaBundle\Model;

use DateTimeInterface;

interface MediaInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getProvider(): ?string;

    public function getType(): ?string;

    public function getProviderReference(): ?string;

    public function getProviderMetaData(): array;

    public function getImage(): ?string;

    public function getImageMetaData(): array;

    public function getFocalPoint(): ?string;

    public function getCopyright(): ?string;

    public function getAuthorName(): ?string;

    public function getCreatedAt(): DateTimeInterface;

    public function getUpdatedAt(): DateTimeInterface;
}
