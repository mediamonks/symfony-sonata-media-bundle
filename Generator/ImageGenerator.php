<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ImageGenerator
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var FilenameGeneratorInterface
     */
    private $filenameGenerator;

    /**
     * @var array
     */
    private $defaultImageParameters;

    /**
     * @var string
     */
    private $tmpPath;

    /**
     * @var string
     */
    private $tmpPrefix;

    /**
     * @var string
     */
    private $fallbackImage;

    /**
     * @param Server $server
     * @param FilenameGeneratorInterface $filenameGenerator
     * @param array $defaultImageParameters
     * @param null $fallbackImage
     * @param null $tmpPath
     * @param null $tmpPrefix
     */
    public function __construct(
        Server $server,
        FilenameGeneratorInterface $filenameGenerator,
        $defaultImageParameters = [],
        $fallbackImage = null,
        $tmpPath = null,
        $tmpPrefix = null
    ) {
        $this->server = $server;
        $this->filenameGenerator = $filenameGenerator;
        $this->defaultImageParameters = $defaultImageParameters;
        $this->fallbackImage = $fallbackImage;
        $this->tmpPath = $tmpPath;
        $this->tmpPrefix = $tmpPrefix;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBag $parameterBag
     * @return mixed
     */
    public function generate(MediaInterface $media, ParameterBag $parameterBag)
    {
        $parameterBag->setDefaults($this->defaultImageParameters);

        $filename = $this->filenameGenerator->generate($media, $parameterBag);

        if (!$this->server->getSource()->has($filename)) {
            $this->generateImage($media, $parameterBag, $filename);
        }

        return $filename;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBag $parameterBag
     * @param $filename
     * @throws FilesystemException
     * @throws \Exception
     */
    protected function generateImage(MediaInterface $media, ParameterBag $parameterBag, $filename)
    {
        $tmp = $this->getTemporaryFile();
        $imageData = $this->getImageData($media);

        if (@file_put_contents($tmp, $imageData) === false) {
            throw new FilesystemException('Unable to write temporary file');
        }

        try {
            $this->server->getCache()->put($filename, $this->doGenerateImage($media, $tmp, $parameterBag));
        } catch (\Exception $e) {
            throw new \Exception('Could not generate image', 0, $e);
        } finally {
            if (file_exists($tmp)) {
                if (!@unlink($tmp)) {
                    throw new FilesystemException('Unable to clean up temporary file');
                }
            }
        }
    }

    /**
     * @param MediaInterface $media
     * @return string
     * @throws FilesystemException
     */
    protected function getImageData(MediaInterface $media)
    {
        if ($this->server->getSource()->has($media->getImage())) {
            return $this->server->getSource()->read($media->getImage());
        }

        if (!is_null($this->fallbackImage)) {
            return file_get_contents($this->fallbackImage);
        }

        throw new FilesystemException('File not found');
    }

    /**
     * @param MediaInterface $media
     * @param string $tmp
     * @param ParameterBag $parameterBag
     * @return string
     */
    protected function doGenerateImage(MediaInterface $media, $tmp, ParameterBag $parameterBag)
    {
        $parameters = $parameterBag->getExtra();
        $parameters['w'] = $parameterBag->getWidth();
        $parameters['h'] = $parameterBag->getHeight();

        return $this->server->getApi()->run($tmp, $parameters);
    }

    /**
     * @return string
     */
    protected function getTemporaryFile()
    {
        if (empty($this->tmpPath)) {
            $this->tmpPath = sys_get_temp_dir();
        }
        if (empty($this->tmpPrefix)) {
            $this->tmpPrefix = 'media';
        }

        return @tempnam($this->tmpPath, $this->tmpPrefix);
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return FilenameGeneratorInterface
     */
    public function getFilenameGenerator()
    {
        return $this->filenameGenerator;
    }

    /**
     * @return array
     */
    public function getDefaultImageParameters()
    {
        return $this->defaultImageParameters;
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        return $this->tmpPath;
    }

    /**
     * @return string
     */
    public function getTmpPrefix()
    {
        return $this->tmpPrefix;
    }

    /**
     * @return string
     */
    public function getFallbackImage()
    {
        return $this->fallbackImage;
    }
}
