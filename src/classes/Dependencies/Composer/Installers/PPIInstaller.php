<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class PPIInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
