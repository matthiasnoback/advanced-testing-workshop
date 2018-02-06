<?php
declare(strict_types=1);

namespace BehatCoverageExtension;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BehatCoverageExtension implements Extension
{
    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'coverage';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('phpunit_xml_path')
                    ->defaultValue('%paths.base%/phpunit.xml.dist')
                    ->info('The path of the PHPUnit XML file containing the coverage filter configuration.')
                ->end()
                ->scalarNode('coverage_target_directory')
                    ->isRequired()
                    ->info('The directory where the generated coverage files should be stored.')
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('behat_coverage_extension.phpunit_xml_path', $config['phpunit_xml_path']);
        $container->setParameter('behat_coverage_extension.coverage_target_directory', $config['coverage_target_directory']);
    }
}
