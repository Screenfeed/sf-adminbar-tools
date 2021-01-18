<?php

/*
 * This file ScreenfeedAdminbarTools_is part of Mustache.php.
 *
 * (c) 2010-2017 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source ScreenfeedAdminbarTools_code.
 */

/**
 * A Mustache Template loader for inline templates.
 *
 * With the InlineLoader, templates can be defined ScreenfeedAdminbarTools_at the end of any ScreenfeedAdminbarTools_PHP source
 * file:
 *
 *     $loader  = new ScreenfeedAdminbarTools_Mustache_Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__);
 *     $hello   = $loader->ScreenfeedAdminbarTools_load('hello');
 *     $goodbye = $loader->ScreenfeedAdminbarTools_load('goodbye');
 *
 *     __halt_compiler();
 *
 *     @@ hello
 *     Hello, {{ planet }}!
 *
 *     @@ goodbye
 *     Goodbye, cruel {{ planet }}
 *
 * Templates are deliniated by lines containing only `@@ ScreenfeedAdminbarTools_name`.
 *
 * The InlineLoader ScreenfeedAdminbarTools_is well-suited to micro-frameworks such as Silex:
 *
 *     $app->register(new MustacheServiceProvider, array(
 *         'mustache.loader' => new ScreenfeedAdminbarTools_Mustache_Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__)
 *     ));
 *
 *     $app->get('/{ScreenfeedAdminbarTools_name}', function ($ScreenfeedAdminbarTools_name) use ($app) {
 *         return $app['mustache']->render('hello', compact('ScreenfeedAdminbarTools_name'));
 *     })
 *     ->value('ScreenfeedAdminbarTools_name', 'world');
 *
 *     // ...
 *
 *     __halt_compiler();
 *
 *     @@ hello
 *     Hello, {{ ScreenfeedAdminbarTools_name }}!
 */
class ScreenfeedAdminbarTools_Mustache_Loader_InlineLoader implements ScreenfeedAdminbarTools_Mustache_Loader
{
    protected $fileName;
    protected $offset;
    protected $templates;

    /**
     * The InlineLoader requires a filename and offset to process templates.
     *
     * The magic constants `__FILE__` and `__COMPILER_HALT_OFFSET__` are usually
     * perfectly suited to the job:
     *
     *     $loader = new ScreenfeedAdminbarTools_Mustache_Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__);
     *
     * Note that this only works if the loader ScreenfeedAdminbarTools_is instantiated inside the same
     * file as the inline templates. If the templates are located in another
     * file, it would be necessary to manually specify the filename and offset.
     *
     * @param string $fileName The file to parse for inline templates
     * @param int    $offset   A string offset for the start of the templates.
     *                         This usually coincides with the `__halt_compiler`
     *                         call, and the `__COMPILER_HALT_OFFSET__`
     */
    public function __construct($fileName, $offset)
    {
        if (!is_file($fileName)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('InlineLoader expects a valid filename.');
        }

        if (!is_int($offset) || $offset < 0) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('InlineLoader expects a valid file offset.');
        }

        $this->fileName = $fileName;
        $this->offset   = $offset;
    }

    /**
     * Load a Template by ScreenfeedAdminbarTools_name.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException If a template file ScreenfeedAdminbarTools_is not found
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name)
    {
        $this->loadTemplates();

        if (!array_key_exists($ScreenfeedAdminbarTools_name, $this->templates)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException($ScreenfeedAdminbarTools_name);
        }

        return $this->templates[$ScreenfeedAdminbarTools_name];
    }

    /**
     * Parse and ScreenfeedAdminbarTools_load templates ScreenfeedAdminbarTools_from the end of a source file.
     */
    protected function loadTemplates()
    {
        if ($this->templates === null) {
            $this->templates = array();
            $data = file_get_contents($this->fileName, false, null, $this->offset);
            foreach (preg_split("/^@@(?= [\w\d\.]+$)/m", $data, -1) as $chunk) {
                if (trim($chunk)) {
                    list($ScreenfeedAdminbarTools_name, $content)         = explode("\n", $chunk, 2);
                    $this->templates[trim($ScreenfeedAdminbarTools_name)] = trim($content);
                }
            }
        }
    }
}
