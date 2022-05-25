<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\ErrorHandlerTrait;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Throwable;

class ImageGenerator
{
    use ErrorHandlerTrait;

    private Server $server;
    private FilenameGeneratorInterface $filenameGenerator;
    private array $defaultImageParameters;
    private ?string $tmpPath;
    private ?string $tmpPrefix;
    private ?string $fallbackImage;

    /**
     * @param Server $server
     * @param FilenameGeneratorInterface $filenameGenerator
     * @param array $defaultImageParameters
     * @param string|null $fallbackImage
     * @param string|null $tmpPath
     * @param string|null $tmpPrefix
     */
    public function __construct(
        Server $server,
        FilenameGeneratorInterface $filenameGenerator,
        array $defaultImageParameters = [],
        ?string $fallbackImage = null,
        ?string $tmpPath = null,
        ?string $tmpPrefix = null
    )
    {
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
     *
     * @return string
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function generate(MediaInterface $media, ImageParameterBag $parameterBag): string
    {
        $parameterBag->setDefaults($this->defaultImageParameters);
        if (!$parameterBag->hasExtra('fit')) {
            $parameterBag->addExtra('fit', 'crop-' . $media->getFocalPoint());
        }
        if (!$parameterBag->hasExtra('fm') && isset($media->getProviderMetaData()['originalExtension'])) {
            $parameterBag->addExtra('fm', $media->getProviderMetaData()['originalExtension']);
        }

        $filename = $this->filenameGenerator->generate($media, $parameterBag);

        if (!$this->server->getCache()->fileExists($filename)) {
            $this->generateImage($media, $parameterBag, $filename);
        }

        return $filename;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     * @param string $filename
     *
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    protected function generateImage(MediaInterface $media, ImageParameterBag $parameterBag, string $filename): void
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
            $this->server->getCache()->write($filename, $this->doGenerateImage($media, $tmp, $parameterBag));
        } catch (Throwable $e) {
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
     *
     * @return string
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    protected function getImageData(MediaInterface $media): string
    {
        if ($this->server->getSource()->fileExists($media->getImage())) {
            return $this->server->getSource()->read($media->getImage());
        }

        if (!is_null($this->fallbackImage)) {
            return file_get_contents($this->fallbackImage);
        }

        throw new FilesystemException('File not found');
    }

    /**
     * Returns the generated asset encoded.
     *
     * @param MediaInterface $media
     * @param mixed $tmp This property may be of have many formats. Check Intervention\Image\AbstractDecoder::init
     * @param ImageParameterBag $parameterBag
     *
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
     * @return string|null
     */
    public function getTmpPath(): ?string
    {
        return $this->tmpPath;
    }

    /**
     * @return string|null
     */
    public function getTmpPrefix(): ?string
    {
        return $this->tmpPrefix;
    }

    /**
     * @return string|null
     */
    public function getFallbackImage(): ?string
    {
        return $this->fallbackImage;
    }
}
