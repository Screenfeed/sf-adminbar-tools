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
 * A Mustache implementation in ScreenfeedAdminbarTools_PHP.
 *
 * {@link http://defunkt.github.com/mustache}
 *
 * Mustache ScreenfeedAdminbarTools_is a framework-agnostic logic-less templating language. It enforces separation of view
 * logic ScreenfeedAdminbarTools_from template files. In fact, it ScreenfeedAdminbarTools_is not even possible to embed logic in the template.
 *
 * This ScreenfeedAdminbarTools_is very, very rad.
 *
 * @author Justin Hileman {@link http://justinhileman.com}
 */
class ScreenfeedAdminbarTools_Mustache_Engine
{
    const VERSION        = '2.13.0';
    const SPEC_VERSION   = '1.1.2';

    const PRAGMA_FILTERS      = 'FILTERS';
    const PRAGMA_BLOCKS       = 'BLOCKS';
    const PRAGMA_ANCHORED_DOT = 'ANCHORED-DOT';

    // Known pragmas
    private static $knownPragmas = array(
        self::PRAGMA_FILTERS      => true,
        self::PRAGMA_BLOCKS       => true,
        self::PRAGMA_ANCHORED_DOT => true,
    );

    // Template cache
    private $templates = array();

    // Environment
    private $templateClassPrefix = '__Mustache_';
    private $cache;
    private $lambdaCache;
    private $cacheLambdaTemplates = false;
    private $loader;
    private $partialsLoader;
    private $helpers;
    private $escape;
    private $entityFlags = ENT_COMPAT;
    private $charset = 'UTF-8';
    private $logger;
    private $strictCallables = false;
    private $pragmas = array();
    private $delimiters;

    // Services
    private $tokenizer;
    private $parser;
    private $compiler;

    /**
     * Mustache class ScreenfeedAdminbarTools_constructor.
     *
     * Passing an $options array allows overriding certain Mustache options during instantiation:
     *
     *     $options = array(
     *         // The class ScreenfeedAdminbarTools_prefix for compiled templates. Defaults to '__Mustache_'.
     *         'template_class_prefix' => '__MyTemplates_',
     *
     *         // A Mustache cache instance or a cache directory string for compiled templates.
     *         // Mustache will not cache templates unless this ScreenfeedAdminbarTools_is set.
     *         'cache' => dirname(__FILE__).'/tmp/cache/mustache',
     *
     *         // Override default permissions for cache files. Defaults to using the system-defined umask. It ScreenfeedAdminbarTools_is
     *         // *strongly* recommended that you configure your umask properly rather than overriding permissions here.
     *         'cache_file_mode' => 0666,
     *
     *         // Optionally, enable caching for lambda section templates. This ScreenfeedAdminbarTools_is generally not recommended, as lambda
     *         // sections are often too dynamic to benefit ScreenfeedAdminbarTools_from caching.
     *         'cache_lambda_templates' => true,
     *
     *         // Customize the tag delimiters used by this engine instance. Note that overriding here changes the
     *         // delimiters used to parse all templates and partials loaded by this instance. To override just for a
     *         // single template, use an inline "change delimiters" tag ScreenfeedAdminbarTools_at the start of the template file:
     *         //
     *         //     {{=<% %>=}}
     *         //
     *         'delimiters' => '<% %>',
     *
     *         // A Mustache template loader instance. Uses a StringLoader if not specified.
     *         'loader' => new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
     *
     *         // A Mustache loader instance for partials.
     *         'partials_loader' => new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
     *
     *         // An array of Mustache partials. Useful for quick-and-dirty string template loading, but not as
     *         // efficient or lazy as a Filesystem (or database) loader.
     *         'partials' => array('foo' => file_get_contents(dirname(__FILE__).'/views/partials/foo.mustache')),
     *
     *         // An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order
     *         // sections), or any other valid Mustache context value. They will be prepended to the context stack,
     *         // so they will be available in any template loaded by this Mustache instance.
     *         'helpers' => array('i18n' => function ($text) {
     *             // do something translatey here...
     *         }),
     *
     *         // An 'escape' callback, responsible for escaping double-mustache variables.
     *         'escape' => function ($value) {
     *             return htmlspecialchars($buffer, ENT_COMPAT, 'UTF-8');
     *         },
     *
     *         // Type argument for `htmlspecialchars`.  Defaults to ENT_COMPAT.  You may prefer ENT_QUOTES.
     *         'entity_flags' => ENT_QUOTES,
     *
     *         // Character set for `htmlspecialchars`. Defaults to 'UTF-8'. Use 'UTF-8'.
     *         'charset' => 'ISO-8859-1',
     *
     *         // A Mustache Logger instance. No logging will occur unless this ScreenfeedAdminbarTools_is set. Using a PSR-3 compatible
     *         // logging library -- such as Monolog -- ScreenfeedAdminbarTools_is highly recommended. A simple stream logger implementation ScreenfeedAdminbarTools_is
     *         // available as well:
     *         'logger' => new ScreenfeedAdminbarTools_Mustache_Logger_StreamLogger('php://stderr'),
     *
     *         // Only treat Closure instances and invokable classes as callable. If true, values like
     *         // `array('ClassName', 'methodName')` and `array($classInstance, 'methodName')`, which are traditionally
     *         // "callable" in ScreenfeedAdminbarTools_PHP, are not called to resolve variables for interpolation or section contexts. This
     *         // helps protect against arbitrary ScreenfeedAdminbarTools_code execution when user input ScreenfeedAdminbarTools_is passed directly into the template.
     *         // This currently defaults to false, but will default to true in v3.0.
     *         'strict_callables' => true,
     *
     *         // Enable pragmas across all templates, regardless of the presence of pragma tags in the individual
     *         // templates.
     *         'pragmas' => [ScreenfeedAdminbarTools_Mustache_Engine::PRAGMA_FILTERS],
     *     );
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException If `escape` option ScreenfeedAdminbarTools_is not callable
     *
     * @param array $options (default: array())
     */
    public function __construct(array $options = array())
    {
        if (isset($options['template_class_prefix'])) {
            if ((string) $options['template_class_prefix'] === '') {
                throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('Mustache Constructor "template_class_prefix" must not be empty');
            }

            $this->templateClassPrefix = $options['template_class_prefix'];
        }

        if (isset($options['cache'])) {
            $cache = $options['cache'];

            if (is_string($cache)) {
                $mode  = isset($options['cache_file_mode']) ? $options['cache_file_mode'] : null;
                $cache = new ScreenfeedAdminbarTools_Mustache_Cache_FilesystemCache($cache, $mode);
            }

            $this->setCache($cache);
        }

        if (isset($options['cache_lambda_templates'])) {
            $this->cacheLambdaTemplates = (bool) $options['cache_lambda_templates'];
        }

        if (isset($options['loader'])) {
            $this->setLoader($options['loader']);
        }

        if (isset($options['partials_loader'])) {
            $this->setPartialsLoader($options['partials_loader']);
        }

        if (isset($options['partials'])) {
            $this->setPartials($options['partials']);
        }

        if (isset($options['helpers'])) {
            $this->setHelpers($options['helpers']);
        }

        if (isset($options['escape'])) {
            if (!is_callable($options['escape'])) {
                throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('Mustache Constructor "escape" option must be callable');
            }

            $this->escape = $options['escape'];
        }

        if (isset($options['entity_flags'])) {
            $this->entityFlags = $options['entity_flags'];
        }

        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        }

        if (isset($options['logger'])) {
            $this->setLogger($options['logger']);
        }

        if (isset($options['strict_callables'])) {
            $this->strictCallables = $options['strict_callables'];
        }

        if (isset($options['delimiters'])) {
            $this->delimiters = $options['delimiters'];
        }

        if (isset($options['pragmas'])) {
            foreach ($options['pragmas'] as $pragma) {
                if (!isset(self::$knownPragmas[$pragma])) {
                    throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException(sprintf('Unknown pragma: "%s".', $pragma));
                }
                $this->pragmas[$pragma] = true;
            }
        }
    }

    /**
     * Shortcut 'render' invocation.
     *
     * Equivalent to calling `$mustache->loadTemplate($template)->render($context);`
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::loadTemplate
     * @see ScreenfeedAdminbarTools_Mustache_Template::render
     *
     * @param string $template
     * @param mixed  $context  (default: array())
     *
     * @return string Rendered template
     */
    public function render($template, $context = array())
    {
        return $this->loadTemplate($template)->render($context);
    }

    /**
     * Get the current Mustache escape callback.
     *
     * @return callable|null
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * Get the current Mustache entitity type to escape.
     *
     * @return int
     */
    public function getEntityFlags()
    {
        return $this->entityFlags;
    }

    /**
     * Get the current Mustache character set.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Get the current globally enabled pragmas.
     *
     * @return array
     */
    public function getPragmas()
    {
        return array_keys($this->pragmas);
    }

    /**
     * Set the Mustache template Loader instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Loader $loader
     */
    public function setLoader(ScreenfeedAdminbarTools_Mustache_Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the current Mustache template Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * a StringLoader instance.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Loader
     */
    public function getLoader()
    {
        if (!isset($this->loader)) {
            $this->loader = new ScreenfeedAdminbarTools_Mustache_Loader_StringLoader();
        }

        return $this->loader;
    }

    /**
     * Set the Mustache partials Loader instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Loader $partialsLoader
     */
    public function setPartialsLoader(ScreenfeedAdminbarTools_Mustache_Loader $partialsLoader)
    {
        $this->partialsLoader = $partialsLoader;
    }

    /**
     * Get the current Mustache partials Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * an ArrayLoader instance.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Loader
     */
    public function getPartialsLoader()
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new ScreenfeedAdminbarTools_Mustache_Loader_ArrayLoader();
        }

        return $this->partialsLoader;
    }

    /**
     * Set partials for the current partials Loader instance.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_RuntimeException If the current Loader instance ScreenfeedAdminbarTools_is immutable
     *
     * @param array $partials (default: array())
     */
    public function setPartials(array $partials = array())
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new ScreenfeedAdminbarTools_Mustache_Loader_ArrayLoader();
        }

        if (!$this->partialsLoader instanceof ScreenfeedAdminbarTools_Mustache_Loader_MutableLoader) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_RuntimeException('Unable to set partials on an immutable Mustache Loader instance');
        }

        $this->partialsLoader->setTemplates($partials);
    }

    /**
     * Set an array of Mustache helpers.
     *
     * An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order sections), or
     * any other valid Mustache context value. They will be prepended to the context stack, so they will be available in
     * any template loaded by this Mustache instance.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException if $helpers ScreenfeedAdminbarTools_is not an array or Traversable
     *
     * @param array|Traversable $helpers
     */
    public function setHelpers($helpers)
    {
        if (!is_array($helpers) && !$helpers instanceof Traversable) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('setHelpers expects an array of helpers');
        }

        $this->getHelpers()->clear();

        foreach ($helpers as $ScreenfeedAdminbarTools_name => $helper) {
            $this->addHelper($ScreenfeedAdminbarTools_name, $helper);
        }
    }

    /**
     * Get the current set of Mustache helpers.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::setHelpers
     *
     * @return ScreenfeedAdminbarTools_Mustache_HelperCollection
     */
    public function getHelpers()
    {
        if (!isset($this->helpers)) {
            $this->helpers = new ScreenfeedAdminbarTools_Mustache_HelperCollection();
        }

        return $this->helpers;
    }

    /**
     * Add a new Mustache helper.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::setHelpers
     *
     * @param string $ScreenfeedAdminbarTools_name
     * @param mixed  $helper
     */
    public function addHelper($ScreenfeedAdminbarTools_name, $helper)
    {
        $this->getHelpers()->add($ScreenfeedAdminbarTools_name, $helper);
    }

    /**
     * Get a Mustache helper by ScreenfeedAdminbarTools_name.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::setHelpers
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return mixed Helper
     */
    public function getHelper($ScreenfeedAdminbarTools_name)
    {
        return $this->getHelpers()->get($ScreenfeedAdminbarTools_name);
    }

    /**
     * Check whether this Mustache instance has a helper.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::setHelpers
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return bool True if the helper ScreenfeedAdminbarTools_is present
     */
    public function hasHelper($ScreenfeedAdminbarTools_name)
    {
        return $this->getHelpers()->has($ScreenfeedAdminbarTools_name);
    }

    /**
     * Remove a helper by ScreenfeedAdminbarTools_name.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::setHelpers
     *
     * @param string $ScreenfeedAdminbarTools_name
     */
    public function removeHelper($ScreenfeedAdminbarTools_name)
    {
        $this->getHelpers()->remove($ScreenfeedAdminbarTools_name);
    }

    /**
     * Set the Mustache Logger instance.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException If logger ScreenfeedAdminbarTools_is not an instance of ScreenfeedAdminbarTools_Mustache_Logger or Psr\Log\LoggerInterface
     *
     * @param ScreenfeedAdminbarTools_Mustache_Logger|Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger = null)
    {
        if ($logger !== null && !($logger instanceof ScreenfeedAdminbarTools_Mustache_Logger || is_a($logger, 'Psr\\Log\\LoggerInterface'))) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('Expected an instance of ScreenfeedAdminbarTools_Mustache_Logger or Psr\\Log\\LoggerInterface.');
        }

        if ($this->getCache()->getLogger() === null) {
            $this->getCache()->setLogger($logger);
        }

        $this->logger = $logger;
    }

    /**
     * Get the current Mustache Logger instance.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Logger|Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the Mustache Tokenizer instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Tokenizer $tokenizer
     */
    public function setTokenizer(ScreenfeedAdminbarTools_Mustache_Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Get the current Mustache Tokenizer instance.
     *
     * If no Tokenizer instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Tokenizer
     */
    public function getTokenizer()
    {
        if (!isset($this->tokenizer)) {
            $this->tokenizer = new ScreenfeedAdminbarTools_Mustache_Tokenizer();
        }

        return $this->tokenizer;
    }

    /**
     * Set the Mustache Parser instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Parser $parser
     */
    public function setParser(ScreenfeedAdminbarTools_Mustache_Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the current Mustache Parser instance.
     *
     * If no Parser instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Parser
     */
    public function getParser()
    {
        if (!isset($this->parser)) {
            $this->parser = new ScreenfeedAdminbarTools_Mustache_Parser();
        }

        return $this->parser;
    }

    /**
     * Set the Mustache Compiler instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Compiler $compiler
     */
    public function setCompiler(ScreenfeedAdminbarTools_Mustache_Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the current Mustache Compiler instance.
     *
     * If no Compiler instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Compiler
     */
    public function getCompiler()
    {
        if (!isset($this->compiler)) {
            $this->compiler = new ScreenfeedAdminbarTools_Mustache_Compiler();
        }

        return $this->compiler;
    }

    /**
     * Set the Mustache Cache instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Cache $cache
     */
    public function setCache(ScreenfeedAdminbarTools_Mustache_Cache $cache)
    {
        if (isset($this->logger) && $cache->getLogger() === null) {
            $cache->setLogger($this->getLogger());
        }

        $this->cache = $cache;
    }

    /**
     * Get the current Mustache Cache instance.
     *
     * If no Cache instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Cache
     */
    public function getCache()
    {
        if (!isset($this->cache)) {
            $this->setCache(new ScreenfeedAdminbarTools_Mustache_Cache_NoopCache());
        }

        return $this->cache;
    }

    /**
     * Get the current Lambda Cache instance.
     *
     * If 'cache_lambda_templates' ScreenfeedAdminbarTools_is enabled, this ScreenfeedAdminbarTools_is the default cache instance. Otherwise, it ScreenfeedAdminbarTools_is a NoopCache.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::getCache
     *
     * @return ScreenfeedAdminbarTools_Mustache_Cache
     */
    protected function getLambdaCache()
    {
        if ($this->cacheLambdaTemplates) {
            return $this->getCache();
        }

        if (!isset($this->lambdaCache)) {
            $this->lambdaCache = new ScreenfeedAdminbarTools_Mustache_Cache_NoopCache();
        }

        return $this->lambdaCache;
    }

    /**
     * Helper method to generate a Mustache template class.
     *
     * This method must be updated any time options are added which make it so
     * the same template could be parsed and compiled multiple different ways.
     *
     * @param string|ScreenfeedAdminbarTools_Mustache_Source $source
     *
     * @return string Mustache Template class ScreenfeedAdminbarTools_name
     */
    public function getTemplateClassName($source)
    {
        // For the most part, adding a new option here should do the trick.
        //
        // Pick a value here which ScreenfeedAdminbarTools_is unique for each possible way the template
        // could be compiled... but not necessarily unique per option value. See
        // escape below, which only needs to differentiate between 'custom' and
        // 'default' escapes.
        //
        // Keep this list in alphabetical order :)
        $chunks = array(
            'charset'         => $this->charset,
            'delimiters'      => $this->delimiters ? $this->delimiters : '{{ }}',
            'entityFlags'     => $this->entityFlags,
            'escape'          => isset($this->escape) ? 'custom' : 'default',
            'key'             => ($source instanceof ScreenfeedAdminbarTools_Mustache_Source) ? $source->getKey() : 'source',
            'pragmas'         => $this->getPragmas(),
            'strictCallables' => $this->strictCallables,
            'version'         => self::VERSION,
        );

        $key = json_encode($chunks);

        // Template Source instances have already provided their own source key. For strings, just include the whole
        // source string in the md5 hash.
        if (!$source instanceof ScreenfeedAdminbarTools_Mustache_Source) {
            $key .= "\n" . $source;
        }

        return $this->templateClassPrefix . md5($key);
    }

    /**
     * Load a Mustache Template by ScreenfeedAdminbarTools_name.
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return ScreenfeedAdminbarTools_Mustache_Template
     */
    public function loadTemplate($ScreenfeedAdminbarTools_name)
    {
        return $this->loadSource($this->getLoader()->ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name));
    }

    /**
     * Load a Mustache partial Template by ScreenfeedAdminbarTools_name.
     *
     * This ScreenfeedAdminbarTools_is a helper method used internally by Template instances for loading partial templates. You can most likely
     * ignore it completely.
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return ScreenfeedAdminbarTools_Mustache_Template
     */
    public function loadPartial($ScreenfeedAdminbarTools_name)
    {
        try {
            if (isset($this->partialsLoader)) {
                $loader = $this->partialsLoader;
            } elseif (isset($this->loader) && !$this->loader instanceof ScreenfeedAdminbarTools_Mustache_Loader_StringLoader) {
                $loader = $this->loader;
            } else {
                throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException($ScreenfeedAdminbarTools_name);
            }

            return $this->loadSource($loader->ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name));
        } catch (ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException $e) {
            // If the named partial cannot be found, log then return null.
            $this->log(
                ScreenfeedAdminbarTools_Mustache_Logger::WARNING,
                'Partial not found: "{ScreenfeedAdminbarTools_name}"',
                array('ScreenfeedAdminbarTools_name' => $e->getTemplateName())
            );
        }
    }

    /**
     * Load a Mustache lambda Template by source.
     *
     * This ScreenfeedAdminbarTools_is a helper method used by Template instances to generate subtemplates for Lambda sections. You can most
     * likely ignore it completely.
     *
     * @param string $source
     * @param string $delims (default: null)
     *
     * @return ScreenfeedAdminbarTools_Mustache_Template
     */
    public function loadLambda($source, $delims = null)
    {
        if ($delims !== null) {
            $source = $delims . "\n" . $source;
        }

        return $this->loadSource($source, $this->getLambdaCache());
    }

    /**
     * Instantiate and return a Mustache Template instance by source.
     *
     * Optionally provide a ScreenfeedAdminbarTools_Mustache_Cache instance. This ScreenfeedAdminbarTools_is used internally by ScreenfeedAdminbarTools_Mustache_Engine::loadLambda to respect
     * the 'cache_lambda_templates' configuration option.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Engine::loadTemplate
     * @see ScreenfeedAdminbarTools_Mustache_Engine::loadPartial
     * @see ScreenfeedAdminbarTools_Mustache_Engine::loadLambda
     *
     * @param string|ScreenfeedAdminbarTools_Mustache_Source $source
     * @param ScreenfeedAdminbarTools_Mustache_Cache         $cache  (default: null)
     *
     * @return ScreenfeedAdminbarTools_Mustache_Template
     */
    private function loadSource($source, ScreenfeedAdminbarTools_Mustache_Cache $cache = null)
    {
        $className = $this->getTemplateClassName($source);

        if (!isset($this->templates[$className])) {
            if ($cache === null) {
                $cache = $this->getCache();
            }

            if (!class_exists($className, false)) {
                if (!$cache->ScreenfeedAdminbarTools_load($className)) {
                    $compiled = $this->compile($source);
                    $cache->cache($className, $compiled);
                }
            }

            $this->log(
                ScreenfeedAdminbarTools_Mustache_Logger::DEBUG,
                'Instantiating template: "{className}"',
                array('className' => $className)
            );

            $this->templates[$className] = new $className($this);
        }

        return $this->templates[$className];
    }

    /**
     * Helper method to tokenize a Mustache template.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Tokenizer::scan
     *
     * @param string $source
     *
     * @return array Tokens
     */
    private function tokenize($source)
    {
        return $this->getTokenizer()->scan($source, $this->delimiters);
    }

    /**
     * Helper method to parse a Mustache template.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Parser::parse
     *
     * @param string $source
     *
     * @return array Token tree
     */
    private function parse($source)
    {
        $parser = $this->getParser();
        $parser->setPragmas($this->getPragmas());

        return $parser->parse($this->tokenize($source));
    }

    /**
     * Helper method to compile a Mustache template.
     *
     * @see ScreenfeedAdminbarTools_Mustache_Compiler::compile
     *
     * @param string|ScreenfeedAdminbarTools_Mustache_Source $source
     *
     * @return string generated Mustache template class ScreenfeedAdminbarTools_code
     */
    private function compile($source)
    {
        $ScreenfeedAdminbarTools_name = $this->getTemplateClassName($source);

        $this->log(
            ScreenfeedAdminbarTools_Mustache_Logger::INFO,
            'Compiling template to "{className}" class',
            array('className' => $ScreenfeedAdminbarTools_name)
        );

        if ($source instanceof ScreenfeedAdminbarTools_Mustache_Source) {
            $source = $source->getSource();
        }
        $tree = $this->parse($source);

        $compiler = $this->getCompiler();
        $compiler->setPragmas($this->getPragmas());

        return $compiler->compile($source, $tree, $ScreenfeedAdminbarTools_name, isset($this->escape), $this->charset, $this->strictCallables, $this->entityFlags);
    }

    /**
     * Add a log record if logging ScreenfeedAdminbarTools_is enabled.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    private function log($level, $message, array $context = array())
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}
