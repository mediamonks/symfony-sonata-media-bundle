### Under construction, not yet ready for usage!

# MediaMonksSonataMediaBundle

This bundle is an alternative to the existing Sonata Media Bundle.

The concept of this bundle is to provide an easy method of uploading different kinds of media to your admin with an easy
method of displaying thumbnails and embeds. It is assumed you have a CDN where you place your thumbnails on so only the
first request for a specific thumbnail hits your own server.

## Features

- Supports many file systems to store the media (S3, Azure, Google Cloud, (S)FTP, Rackspace)
- Supports image uploads & YouTube (planned: Vimeo, Facebook, SoundCloud, Mixcloud)
- Generate thumbnails with various options including a focal point
- Caches thumbnails in your CDN by using cache control headers

## Documentation

Please refer to the files in the [/Resources/doc](/Resources/doc) folder.

## System Requirements

You need:

- **PHP >= 5.5.0**
- **Symfony Framework >= 2.8**
- **Sonata Admin >=3**

To use the library.

## Security

If you discover any security related issues, please email devmonk@mediamonks.com instead of using the issue tracker.

## Credits

- [Sonata Project](https://sonata-project.org/) for creating their [Media Bundle](https://github.com/sonata-project/SonataMediaBundle) which was obviously the main inspiration for this bundle.
- [Flysystem](https://flysystem.thephpleague.com/) for accessing different kinds of file systems
- [Glide](http://glide.thephpleague.com/) for providing a great api to do image manipulation

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
