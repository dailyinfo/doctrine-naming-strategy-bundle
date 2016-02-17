<?php
/*
 * This file is part of the Doctrine Naming Strategy Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\DoctrineNamingStrategy\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use RunOpenCode\Bundle\DoctrineNamingStrategy\DependencyInjection\Extension;
use RunOpenCode\Bundle\DoctrineNamingStrategy\NamingStrategy\NamerCollection;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function configureUnderscoredBundlePrefixNamer()
    {
        $configuration = array(
            'case' => 'lowercase',
            'map' => array(
                'MyLongNameOfTheBundle' => 'my_prefix',
                'MyOtherLongNameOfTheBundle' => 'my_prefix_2'
            ),
            'joinTableFieldSuffix' => true,
            'blacklist' =>
                array(
                    'DoNotPrefixThisBundle'
                ),
            'whitelist' => array()
        );

        $this->load(array('underscored_bundle_prefix' => $configuration));

        $this->assertContainerBuilderHasService('run_open_code.doctrine.orm.naming_strategy.underscored_bundle_prefix');

        $configuration['case'] = CASE_LOWER;

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'run_open_code.doctrine.orm.naming_strategy.underscored_bundle_prefix',
            1,
            $configuration
        );
    }

    /**
     * @test
     */
    public function configureUnderscoredClassNamespacePrefixNamer()
    {
        $configuration = array(
            'case' => 'lowercase',
            'map' => array(
                'My\Class\Namespace\Entity' => 'my_prefix'
            ),
            'joinTableFieldSuffix' => true,
            'blacklist' =>
                array(
                    'My\Class\Namespace\Entity\ThisShouldBeSkipped',
                    'My\Class\Namespace\Entity\ThisShouldBeSkippedAsWell'
                ),
            'whitelist' => array()
        );

        $this->load(array('underscored_class_namespace_prefix' => $configuration));

        $this->assertContainerBuilderHasService('run_open_code.doctrine.orm.naming_strategy.underscored_class_namespace_prefix');

        $configuration['case'] = CASE_LOWER;

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'run_open_code.doctrine.orm.naming_strategy.underscored_class_namespace_prefix',
            0,
            $configuration
        );
    }

    /**
     * @test
     */
    public function configureNamerCollection()
    {
        $configuration = array(
            'default' => 'doctrine.orm.naming_strategy.underscore',
            'namers' => array(
                'run_open_code.doctrine.orm.naming_strategy.underscored_class_namespace_prefix',
                'run_open_code.doctrine.orm.naming_strategy.underscored_bundle_prefix'
            ),
            'concatenation' => NamerCollection::UNDERSCORE,
            'joinTableFieldSuffix' => true
        );

        $this->load(array('namer_collection' => $configuration));

        $this->assertContainerBuilderHasService('run_open_code.doctrine.orm.naming_strategy.namer_collection');

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'run_open_code.doctrine.orm.naming_strategy.namer_collection',
            1,
            $configuration['namers']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'run_open_code.doctrine.orm.naming_strategy.namer_collection',
            2,
            array(
                'concatenation' => $configuration['concatenation'],
                'joinTableFieldSuffix' => $configuration['joinTableFieldSuffix']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new Extension()
        );
    }
}