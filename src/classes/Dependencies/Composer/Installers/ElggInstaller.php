<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class ElggInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin' => 'mod/{$name}/',
    );
}
