<?php
namespace Screenfeed\AdminbarTools\Dependencies\Composer\Installers;

class RedaxoInstaller extends BaseInstaller
{
    protected $locations = array(
        'addon'          => 'redaxo/include/addons/{$name}/',
        'bestyle-plugin' => 'redaxo/include/addons/be_style/plugins/{$name}/'
    );
}
