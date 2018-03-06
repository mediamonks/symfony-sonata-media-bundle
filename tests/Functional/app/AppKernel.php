<?php

namespace MediaMonks\SonataMediaBundle\Tests\App;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

$loader = require __DIR__.'/../../../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Sonata\CoreBundle\SonataCoreBundle(),
            new \Sonata\AdminBundle\SonataAdminBundle(),
            new \Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \MediaMonks\SonataMediaBundle\MediaMonksSonataMediaBundle(),
            new \MediaMonks\SonataMediaBundle\Tests\AppBundle\AppBundle()
        ];

        if (in_array($this->getEnvironment(), ['test'], true)) {
            $bundles[] = new \Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/../var/cache/%s', $this->rootDir, $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/../var/logs', $this->rootDir);
    }
}