<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class KohanaInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
