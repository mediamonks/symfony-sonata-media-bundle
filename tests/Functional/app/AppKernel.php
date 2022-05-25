<?php

namespace MediaMonks\SonataMediaBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Liip\FunctionalTestBundle\LiipFunctionalTestBundle;
use Liip\TestFixturesBundle\LiipTestFixturesBundle;
use MediaMonks\SonataMediaBundle\MediaMonksSonataMediaBundle;
use MediaMonks\SonataMediaBundle\Tests\AppBundle\AppBundle;
use Oneup\FlysystemBundle\OneupFlysystemBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\CoreBundle\SonataCoreBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use function dirname;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new TwigBundle(),
            new WebProfilerBundle(),
            new SecurityBundle(),
            new DoctrineBundle(),
            new SensioFrameworkExtraBundle(),
            new OneupFlysystemBundle(),
            new SonataBlockBundle(),
            new SonataAdminBundle(),
            new SonataCoreBundle(),
            new SonataDoctrineORMAdminBundle(),
            new KnpMenuBundle(),
            new MediaMonksSonataMediaBundle(),
            new AppBundle(),
            new MonologBundle()
        ];

        if (in_array($this->getEnvironment(), ['test'], true)) {
            $bundles[] = new LiipFunctionalTestBundle();
            $bundles[] = new LiipTestFixturesBundle();
            $bundles[] = new DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getProjectDir() . '/app/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/var/cache/%s', $this->getProjectDir(), $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/var/logs', $this->getProjectDir());
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }
}