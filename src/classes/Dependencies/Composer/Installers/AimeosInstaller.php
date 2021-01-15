<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class AimeosInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension'   => 'ext/{$name}/',
    );
}
