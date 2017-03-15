[![Build Status](https://travis-ci.org/mediamonks/symfony-sonata-media-bundle.svg?branch=master)](https://travis-ci.org/mediamonks/symfony-sonata-media-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/symfony-sonata-media-bundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/sonata-media-bundle/downloads)](https://packagist.org/packages/mediamonks/crawler-bundle)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/sonata-media-bundle/v/stable)](https://packagist.org/packages/mediamonks/crawler-bundle)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/sonata-media-bundle/v/unstable)](https://packagist.org/packages/mediamonks/crawler-bundle)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/3840ec2c-d443-4f15-a786-d9206614fe1d.svg)](https://insight.sensiolabs.com/projects/3840ec2c-d443-4f15-a786-d9206614fe1d)
[![License](https://poser.pugx.org/mediamonks/sonata-media-bundle/license)](https://packagist.org/packages/mediamonks/sonata-media-bundle)


### Under construction, this has not been tested in production and is under development.

# MediaMonksSonataMediaBundle

This bundle is an alternative to the existing Sonata Media Bundle.

The concept of this bundle is to provide an easy method of uploading different kinds of media to your admin with an easy
method of displaying thumbnails and embeds. It is assumed you have some kind of persistent storage behind a CDN or reverse 
proxy where you place your images on and your CDN or reverse proxy can cache redirect headers to prevent end users hitting
your web servers.

## Features

- Supports many [file systems](http://flysystem.thephpleague.com/adapter/local/) to store the media (S3, Azure, Google Cloud, (S)FTP, Rackspace)
- Supports image uploads, file uploads, YouTube, Vimeo & SoundCloud 
- Supports a private and public storage
- Generate thumbnails with [various options](http://glide.thephpleague.com/1.0/api/quick-reference/)
- Caches redirects to your images in your CDN or reverse proxy by using cache control headers
- Embed media with Twig filters

## Documentation

Please refer to the files in the [/Resources/doc](/Resources/doc) folder.

## System Requirements

You need:

- **PHP >= 5.5.0**
- **Symfony Framework >= 2.8**
- **Sonata Admin >= 3.0**
- **Flysystem >= 1.0**
- **Glide >= 1.2**

To use the library.

## Security

If you discover any security related issues, please email devmonk@mediamonks.com instead of using the issue tracker.

## Credits

- [Sonata Project](https://sonata-project.org/) for creating their [Media Bundle](https://github.com/sonata-project/SonataMediaBundle) which was obviously the main inspiration for this bundle.
- [Flysystem](https://flysystem.thephpleague.com/) for accessing different kinds of file systems
- [Glide](http://glide.thephpleague.com/) for providing a great api to do image manipulation

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
