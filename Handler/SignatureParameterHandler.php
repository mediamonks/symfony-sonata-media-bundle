<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Exception\SignatureInvalidException;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;
use Mockery\Generator\Parameter;

class SignatureParameterHandler implements ParameterHandlerInterface
{
    const PARAMETER_SIGNATURE = 's';
    const PARAMETER_BUST_CACHE = 'bc';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @param string $key
     * @param string $hashAlgorithm
     */
    public function __construct($key, $hashAlgorithm = 'sha256')
    {
        $this->key = $key;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @return array
     */
    public function getRouteParameters(MediaInterface $media, ParameterBagInterface $parameterBag)
    {
        $parameters = $parameterBag->toArray($media);
        $parameters[self::PARAMETER_SIGNATURE] = $this->calculateSignature($parameters);

        return $parameters;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @return ImageParameterBag|ParameterBagInterface
     * @throws SignatureInvalidException
     */
    public function validateParameterBag(MediaInterface $media, ParameterBagInterface $parameterBag)
    {
        $data = $parameterBag->toArray($media);

        if (!isset($data[self::PARAMETER_SIGNATURE])) {
            throw new SignatureInvalidException();
        }

        $signature = $data[self::PARAMETER_SIGNATURE];
        $parameterBag->removeExtra(self::PARAMETER_SIGNATURE);

        if (!$this->isValid($media, $parameterBag, $signature)) {
            throw new SignatureInvalidException();
        }

        return $parameterBag;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @param $expectedSignature
     * @return bool
     */
    private function isValid(MediaInterface $media, ParameterBagInterface $parameterBag, $expectedSignature)
    {
        return hash_equals($this->calculateSignature($parameterBag->toArray($media)), $expectedSignature);
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function calculateSignature(array $parameters)
    {
        return hash_hmac($this->hashAlgorithm, $this->key, json_encode($this->normalize($parameters)));
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function normalize(array $parameters)
    {
        if (isset($parameters[self::PARAMETER_BUST_CACHE])) {
            unset($parameters[self::PARAMETER_BUST_CACHE]);
        }
        ksort($parameters);

        return array_map('strval', $parameters);
    }
}
