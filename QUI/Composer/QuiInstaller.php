<?php

/**
 * This file contains the \QUI\Installer class
 */

namespace QUI\Composer;

use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

file_put_contents('/var/www/tests/quiqqer/test', 'huhu');

/**
 * QUIQQER Installation
 *
 * @author www.pcsg.de (Henning Leutz)
 * @package com.pcsg.qui
 */

class QuiInstaller extends LibraryInstaller
{
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return '';
    }

    public function supports($packageType)
    {
        return ($packageType === 'quiqqer-system');
    }

	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
        parent::install($repo, $package);
    }

    public function update (InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
    }
}

?>