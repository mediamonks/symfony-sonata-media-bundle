<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use MediaMonks\SonataMediaBundle\DependencyInjection\Compiler\ProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Mockery as m;

class ProviderPassTest extends AbstractCompilerPassTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderPass());
    }

    public function testIfCompilerPassAddsMethodCalls()
    {
        $this->compileContainer();
    }

    public function testIfCompilerPassUsesImageConstraintsFromConfig()
    {
        $this->compileContainer([
            'image_constraints' => ['foo' => 'bar']
        ]);
    }

    private function compileContainer($config = [[]])
    {
        $this->setParameter('mediamonks.sonata_media.config', $config);

        if (empty($config['image_constraints'])) {
            $config['image_constraints'] = [];
        }

        $provider = m::mock(Definition::class);
        $provider->shouldReceive('isLazy')->andReturn(false);
        $provider->shouldReceive('isSynthetic')->andReturn(false);
        $provider->shouldReceive('isPrivate')->andReturn(false);
        $provider->shouldReceive('isPublic')->andReturn(true);
        $provider->shouldReceive('getClass')->andReturn(self::class);
        $provider->shouldReceive('getInstanceofConditionals')->andReturn([]);
        $provider->shouldReceive('isAutoconfigured')->andReturn(true);
        $provider->shouldReceive('hasTag')->withArgs(['sonata_media.provider'])->andReturn(true);
        $provider->shouldReceive('getTag')->withArgs(['sonata_media.provider'])->andReturn(true);
        $provider->shouldReceive('addMethodCall')->once()->withArgs(['setFilesystem', \Mockery::any()]);
        $provider->shouldReceive('addMethodCall')->once()->withArgs(['setImageConstraintOptions', [$config['image_constraints']]]);
        $provider->shouldReceive('addMethodCall')->once()->withArgs(['setTranslator', \Mockery::any()]);
        $provider->shouldReceive('addMethodCall')->once()->withArgs(['setHttpClient', \Mockery::any()]);
        $provider->shouldReceive('addMethodCall')->once()->withArgs(['setFileLocator', \Mockery::any()]);

        $provider->shouldReceive('hasTag')->withArgs(['container.env_var_processor'])->andReturn(false);

        $this->setDefinition('provider', $provider);

        $this->compile();

        $this->assertTrue(true);
    }
}
