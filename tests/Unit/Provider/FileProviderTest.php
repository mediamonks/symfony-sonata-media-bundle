<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use League\Flysystem\Filesystem;
use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Provider\FileProvider;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileProviderTest extends TestCase
{
    public function testGetImageByExtension()
    {
        $method = $this->getMethod('getImageByExtension');
        $provider = new FileProvider();

        $data = [
            'archive.png' => ['zip', 'rar', 'tar', 'gz'],
            'audio.png' => ['wav', 'mp3', 'flac', 'aac', 'aiff', 'm4a', 'ogg', 'oga', 'wma'],
            'code.png' => ['php', 'html', 'css', 'js', 'vb', 'phar', 'py', 'jar', 'json', 'yml'],
            'excel.png' => ['xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm'],
            'image.png' => ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'ai', 'psd'],
            'movie.png' => ['mp4', 'avi', 'mkv', 'mpg', 'mpeg'],
            'pdf.png' => ['pdf'],
            'powerpoint.png' => ['ppt', 'pot', 'pos', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm'],
            'text.png' => ['txt'],
            'word.png' => ['doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb'],
            'default.png' => ['foo', 'bar']
        ];

        foreach ($data as $expected => $extensions) {
            foreach ($extensions as $extension) {
                $this->assertEquals($expected, $method->invokeArgs($provider, [$extension]));
            }
        }
    }

    public function testWriteToFilesystem()
    {
        $this->expectException(FilesystemException::class);

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('writeStream')->andReturn(0);

        $file = m::mock(UploadedFile::class);
        $file->shouldReceive('getRealPath')->andReturn('/foo');

        $provider = new FileProvider();
        $provider->setFilesystem($filesystem);
        $method = $this->getMethod('writeToFilesystem');
        $method->invokeArgs($provider, [$file, 'foo']);
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass(FileProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
