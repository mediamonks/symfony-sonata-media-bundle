[![Build Status](https://travis-ci.org/mediamonks/symfony-sonata-media-bundle.svg?branch=master)](https://travis-ci.org/mediamonks/symfony-sonata-media-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/sonata-media-bundle/downloads)](https://packagist.org/packages/mediamonks/sonata-media-bundle)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/sonata-media-bundle/v/stable)](https://packagist.org/packages/mediamonks/sonata-media-bundle)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/sonata-media-bundle/v/unstable)](https://packagist.org/packages/mediamonks/sonata-media-bundle)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/3840ec2c-d443-4f15-a786-d9206614fe1d.svg)](https://insight.sensiolabs.com/projects/3840ec2c-d443-4f15-a786-d9206614fe1d)
[![License](https://poser.pugx.org/mediamonks/sonata-media-bundle/license)](https://packagist.org/packages/mediamonks/sonata-media-bundle)

# MediaMonksSonataMediaBundle

This bundle is an alternative to the existing Sonata Media Bundle.

The concept of this bundle is to provide an easy method of uploading different kinds of media to your admin with an easy
method of displaying thumbnails and embeds. It is assumed you have some kind of persistent storage behind a CDN or reverse 
proxy where you place your images on and your CDN or reverse proxy can cache redirect headers to prevent end users hitting
your web servers.

## Features

- Supports many [file systems](https://flysystem.thephpleague.com/) to store the media (S3, Azure, Google Cloud, (S)FTP, Rackspace)
- Supports image uploads, file uploads, YouTube, Vimeo & SoundCloud
- Supports a private and public storage
- Generate thumbnails with [various options](http://glide.thephpleague.com/2.0/api/quick-reference/)
- Caches redirects to your images in your CDN or reverse proxy by using cache control headers
- Embed media with Twig filters

## Documentation

Please refer to the files in the [/docs](/docs) folder.

## System Requirements

You need:

- **PHP >= 7.4**
- **Symfony Framework >= 4.3**
- **Sonata Admin >= 4.0**
- **Flysystem >= 4.0**
- **Glide >= 2.0**

To use the library.

## Security

If you discover any security related issues, please email devmonk@mediamonks.com instead of using the issue tracker.

## Credits

- [Sonata Project](https://sonata-project.org/) for creating their [Media Bundle](https://github.com/sonata-project/SonataMediaBundle) which was obviously the main inspiration for this alternative bundle
- [Flysystem](https://flysystem.thephpleague.com/) for accessing different kinds of file systems
- [Glide](http://glide.thephpleague.com/) for providing a great api to do image manipulation

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
