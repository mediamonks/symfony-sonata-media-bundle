<?php

namespace MediaMonks\SonataMediaBundle\Tests\Provider;

use MediaMonks\SonataMediaBundle\Provider\FileProvider;

class FileProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetImageByExtension()
    {
        $method = $this->getMethod('getImageByExtension');
        $obj = new FileProvider();

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
            'word.png' => ['doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb']
        ];

        foreach ($data as $expected => $extensions) {
            foreach ($extensions as $extension) {
                $this->assertEquals($expected, $method->invokeArgs($obj, [$extension]));
            }
        }
    }

    protected static function getMethod($name) {
        $class = new \ReflectionClass(FileProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
