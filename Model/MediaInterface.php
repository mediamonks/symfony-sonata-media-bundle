<?php

namespace MediaMonks\SonataMediaBundle\Model;

interface MediaInterface
{
    public function getId();

    public function getTitle();

    public function getDescription();

    public function getImage();

    public function getAuthorName();

    public function getCopyright();

    public function getProviderName();

    public function getProviderReference();

    public function getProviderMetaData();

    public function getTags();

    public function getPointOfInterest();

    public function getDefaultImageOptions();

    public function getDefaultUrlParameters();

    public function getCreatedAt();

    public function getUpdatedAt();
}