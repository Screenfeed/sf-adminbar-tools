<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class AttogramInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
