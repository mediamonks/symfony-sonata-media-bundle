<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Exception\SignatureInvalidException;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @param $key
     * @param string $hashAlgorithm
     */
    public function __construct($key, $hashAlgorithm = 'sha256')
    {
        $this->key = $key;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * @param ParameterBag $parameterBag
     * @return array
     */
    public function getRouteParameters(ParameterBag $parameterBag)
    {
        $parameters = $parameterBag->toArray();
        $parameters[self::PARAMETER_SIGNATURE] = $this->calculateSignature($parameters);

        return $parameters;
    }

    /**
     * @param $id
     * @param $width
     * @param $height
     * @param array $extra
     * @return ParameterBag
     * @throws SignatureInvalidException
     */
    public function getPayload($id, $width, $height, array $extra)
    {
        if (!isset($extra[self::PARAMETER_SIGNATURE])) {
            throw new SignatureInvalidException();
        }

        $signature = $extra[self::PARAMETER_SIGNATURE];
        unset($extra[self::PARAMETER_SIGNATURE]);

        $parameters = new ParameterBag($id, $width, $height, $extra);
        if (!$this->isValid($parameters, $signature)) {
            throw new SignatureInvalidException();
        }

        return $parameters;
    }

    /**
     * @param ParameterBag $parameters
     * @param $expectedSignature
     * @return bool
     */
    private function isValid(ParameterBag $parameters, $expectedSignature)
    {
        return hash_equals($this->calculateSignature($parameters->toArray()), $expectedSignature);
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
