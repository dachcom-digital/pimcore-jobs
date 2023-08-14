<?php

namespace JobsBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use JobsBundle\DependencyInjection\Compiler\ConnectorDefinitionPass;
use JobsBundle\DependencyInjection\Compiler\ContextItemsResolverPass;
use JobsBundle\Tool\Install;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JobsBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME = 'dachcom-digital/jobs';

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    public function build(ContainerBuilder $container): void
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new ConnectorDefinitionPass());
        $container->addCompilerPass(new ContextItemsResolverPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    protected function configureDoctrineExtension(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createYamlMappingDriver(
                [$this->getNameSpacePath() => $this->getNamespaceName()],
                ['jobs.persistence.doctrine.manager'],
                'jobs.persistence.doctrine.enabled'
            )
        );
    }

    protected function getNamespaceName(): string
    {
        return 'JobsBundle\Model';
    }

    protected function getNameSpacePath(): string
    {
        return realpath(sprintf('%s/config/doctrine/%s', $this->getPath(), 'model'));
    }
}
