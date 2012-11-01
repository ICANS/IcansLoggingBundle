<?php
/**
 * Declares the IcansLoggingBundle
 *
 * @author Sebastian Latza
 * @author Sebastian Pleschko
 * @author Oliver Peymann
 * @copyright   ICANS GmbH
 * @version     $Id: $
 */
namespace ICANS\Bundle\IcansLoggingBundle;

use ICANS\Bundle\IcansLoggingBundle\DependencyInjection\Compiler\HandlingFilterProviderPass;
use ICANS\Bundle\IcansLoggingBundle\DependencyInjection\Compiler\PostprocessorProviderPass;
use ICANS\Bundle\IcansLoggingBundle\DependencyInjection\Compiler\WriteFilterProviderPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle for the logging service
 *
 */
class IcansLoggingBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new HandlingFilterProviderPass());
        $container->addCompilerPass(new WriteFilterProviderPass());
        $container->addCompilerPass(new PostprocessorProviderPass());
    }
}