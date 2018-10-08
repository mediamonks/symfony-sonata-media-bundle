<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\ErrorHandlerTrait;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ImageGenerator
{
    use ErrorHandlerTrait;

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
     * @param ImageParameterBag $parameterBag
     * @return string
     * @throws FilesystemException
     */
    public function generate(MediaInterface $media, ImageParameterBag $parameterBag): string
    {
        $parameterBag->setDefaults($this->defaultImageParameters);
        if (!$parameterBag->hasExtra('fit')) {
            $parameterBag->addExtra('fit', 'crop-'.$media->getFocalPoint());
        }
        if (!$parameterBag->hasExtra('fm') && isset($media->getProviderMetaData()['originalExtension'])) {
            $parameterBag->addExtra('fm', $media->getProviderMetaData()['originalExtension']);
        }

        $filename = $this->filenameGenerator->generate($media, $parameterBag);

        if (!$this->server->getCache()->has($filename)) {
            $this->generateImage($media, $parameterBag, $filename);
        }

        return $filename;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     * @param $filename
     *
     * @throws FilesystemException
     * @throws \Exception
     */
    protected function generateImage(MediaInterface $media, ImageParameterBag $parameterBag, string $filename)
    {
        $tmp = $this->getTemporaryFile();
        $imageData = $this->getImageData($media);

        $this->disableErrorHandler();
        if (file_put_contents($tmp, $imageData) === false) {
            $this->restoreErrorHandler();
            throw new FilesystemException('Unable to write temporary file');
        }
        $this->restoreErrorHandler();

        try {
            $this->server->getCache()->put($filename, $this->doGenerateImage($media, $tmp, $parameterBag));
        } catch (\Exception $e) {
            throw new FilesystemException('Could not generate image', 0, $e);
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
    protected function getImageData(MediaInterface $media): string
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
     * @param ImageParameterBag $parameterBag
     * @return string
     */
    protected function doGenerateImage(MediaInterface $media, $tmp, ImageParameterBag $parameterBag): string
    {
        $parameters = $parameterBag->getExtra();
        $parameters['w'] = $parameterBag->getWidth();
        $parameters['h'] = $parameterBag->getHeight();

        return $this->server->getApi()->run($tmp, $parameters);
    }

    /**
     * @return string
     */
    protected function getTemporaryFile(): string
    {
        if (empty($this->tmpPath)) {
            $this->tmpPath = sys_get_temp_dir();
        }
        if (empty($this->tmpPrefix)) {
            $this->tmpPrefix = 'media';
        }

        $this->disableErrorHandler();
        $tempFile = tempnam($this->tmpPath, $this->tmpPrefix);
        $this->restoreErrorHandler();

        return $tempFile;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return FilenameGeneratorInterface
     */
    public function getFilenameGenerator(): FilenameGeneratorInterface
    {
        return $this->filenameGenerator;
    }

    /**
     * @return array
     */
    public function getDefaultImageParameters(): array
    {
        return $this->defaultImageParameters;
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }

    /**
     * @return string
     */
    public function getTmpPrefix(): string
    {
        return $this->tmpPrefix;
    }

    /**
     * @return string
     */
    public function getFallbackImage(): string
    {
        return $this->fallbackImage;
    }
}
