<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Installer plugin
 */
class InstallerPlugin implements PluginInterface
{
    /**
     * Composer ref
     *
     * @var Composer
     */
    protected $composer;

    /**
     * io ref
     *
     * @var IOInterface
     */
    protected $io;

    /**
     * Installer ref
     *
     * @var object
     */
    protected $installer;

    /**
     * Activate plugin
     *
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->installer = new ArikaimInstaller($io,$composer);
        $composer->getInstallationManager()->addInstaller($this->installer);
    }

    /**
     * Deactivate plugin
     *
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {        
    }

    /**
     * Uninstall plugin
     *
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
}
