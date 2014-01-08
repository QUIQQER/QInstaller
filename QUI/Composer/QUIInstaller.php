<?php

/**
 * This file contains the \QUI\Composer\QUIInstaller class
 */

namespace QUI\Composer;

use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * QUIQQER Installer
 *
 * @author www.pcsg.de (Henning Leutz)
 * @package com.pcsg.qui
 *
 * @todo htaccess
 * @todo robots.txt
 */

class QUIInstaller extends LibraryInstaller
{
    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return ($packageType === 'quiqqer-system');
    }

    /**
    * {@inheritDoc}
    */
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if ( !$repo->hasPackage( $package ) ) {
            return false;
        }

        if ( !is_readable( $this->getInstallPath( $package ) ) ) {
            return false;
        }

        $installed_path = $this->getInstallPath( $package );

        $dir     = getcwd() .'/../../'; // not the best solution :-/
        $etc_dir = $dir .'etc/';

        if ( !file_exists( $etc_dir .'conf.ini' ) ) {
            return false;
        }

        $ini     = parse_ini_file( $etc_dir .'conf.ini', true );
        $cms_dir = rtrim( $ini['globals']['cms_dir'], '/') .'/';

        // quiqqer is installed
        if ( file_exists( $cms_dir .'index.php' ) &&
             file_exists( $cms_dir .'header.php' ) )
        {
            return true;
        }

        return false;
    }

    /**
     * Install QUIQQER
     * @see Composer\Installer.LibraryInstaller::install()
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        // composer installation
        parent::install( $repo, $package );

        $this->_quiqqer_update( $repo, $package );
    }

    /**
     * Update QUIQQER
     * @see Composer\Installer.LibraryInstaller::update()
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);

        $this->_quiqqer_update( $repo, $target );
    }

    /**
     * The quiqqer update routine
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface $package
     */
    protected function _quiqqer_update(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if ( defined( 'CMS_DIR' ) && file_exists( CMS_DIR .'etc/conf.ini' ) )
        {
            $dir = CMS_DIR;

        } else
        {
            $dir = getcwd() .'/../../'; // not the best solution :-/
        }

        $etc_dir  = $dir .'etc/';
        $Composer = $this->composer; /* @var $Composer Composer */

        if ( !file_exists( $etc_dir .'conf.ini' ) )
        {
            throw new \RuntimeException(
                'Could not find the QUIQQER configuration'
            );
        }

        // quiqqers own installation, move the dirs to its place
        $ini = parse_ini_file( $etc_dir .'conf.ini', true );

        $cms_dir = rtrim( $ini['globals']['cms_dir'], '/') .'/';
        $bin_dir = rtrim( $ini['globals']['bin_dir'], '/') .'/';
        $lib_dir = rtrim( $ini['globals']['lib_dir'], '/') .'/';
        $var_dir = rtrim( $ini['globals']['var_dir'], '/') .'/';

        $admin_dir = $cms_dir .'admin/';

        $temp_name = 'backup_'. date('Y_m_d__H_i_s');
        $temp_dir  = $cms_dir . $temp_name .'/';

        $update_files = array(
            'ajax.php', 'bootstrap.php', 'quiqqer.php', 'image.php', 'index.php'
        );

        mkdir( $temp_dir );

        // backup
        if ( is_dir( $bin_dir ) ) {
            rename( $bin_dir, $temp_dir .'bin' );
        }

        if ( is_dir( $lib_dir ) ) {
            rename( $lib_dir, $temp_dir .'lib' );
        }

        if ( is_dir( $admin_dir ) ) {
            rename( $admin_dir, $temp_dir .'admin' );
        }

        foreach ( $update_files as $file )
        {
            if ( file_exists( $cms_dir . $file ) ) {
                rename( $cms_dir . $file, $temp_dir . $file );
            }
        }

        $package_dir = $this->getInstallPath( $package ) .'/';

        rename( $package_dir .'lib' , $lib_dir );
        rename( $package_dir .'bin' , $bin_dir );
        rename( $package_dir .'admin' , $admin_dir );

        foreach ( $update_files as $file )
        {
            if ( file_exists( $package_dir . $file ) )
            {
                rename(
                    $package_dir . $file,
                    $cms_dir . $file
                );
            }
        }


        // move the backup to temp
        rename( $temp_dir, $var_dir .'temp/'. $temp_name );
    }
}

?>