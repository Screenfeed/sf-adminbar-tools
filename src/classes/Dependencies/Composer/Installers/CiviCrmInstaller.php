<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class CiviCrmInstaller extends BaseInstaller
{
    protected $locations = array(
        'ext'    => 'ext/{$name}/'
    );
}
