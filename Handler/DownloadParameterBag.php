<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class DownloadParameterBag implements ParameterBagInterface
{
    /**
     * @param MediaInterface $media
     * @return array
     */
    public function toArray(MediaInterface $media)
    {
        return [
            'id' => $media->getId()
        ];
    }
}
