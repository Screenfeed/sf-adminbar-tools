<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class SyliusInstaller extends BaseInstaller
{
    protected $locations = array(
        'theme' => 'themes/{$name}/',
    );
}
