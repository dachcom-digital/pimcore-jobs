<?php

namespace JobsBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use JobsBundle\DependencyInjection\Compiler\ConnectorPass;
use JobsBundle\Tool\Install;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JobsBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'dachcom-digital/jobs';

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Install::class);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new ConnectorPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/jobs/css/admin.css'
        ];
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/jobs/js/plugin.js',
            '/bundles/jobs/js/settingsPanel.js',
            '/bundles/jobs/js/connector/abstractConnector.js',
            '/bundles/jobs/js/connector/google.js',
            '/bundles/jobs/js/connector/facebook.js',
        ];
    }

    /**
     * @param ContainerBuilder $container
     */
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

    /**
     * @return string|null
     */
    protected function getNamespaceName()
    {
        return 'JobsBundle\Model';
    }

    /**
     * @return string
     */
    protected function getNameSpacePath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            'model'
        );
    }
}
