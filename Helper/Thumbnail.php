<?php

namespace MediaMonks\MediaBundle\Helper;

use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;

class Thumbnail
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * Thumbnail constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param array $parameters
     * @throws FilesystemException
     */
    public function createIfNotExists($source, $destination, array $parameters)
    {
        $filesystem = $this->server->getSource();
        if (!$filesystem->has($destination)) {
            $tmp = tempnam(sys_get_temp_dir(), 'media');
            if (@file_put_contents($tmp, $filesystem->read($source)) === false) {
                throw new FilesystemException('unable_to_write_temporary_media_file');
            }
            $filesystem->write($destination, $this->server->getApi()->run($tmp, $parameters));
            @unlink($tmp);
        }
    }
}