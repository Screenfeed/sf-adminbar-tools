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
 * Mustache Parser class.
 *
 * This class ScreenfeedAdminbarTools_is responsible for turning a set of Mustache tokens into a parse tree.
 */
class ScreenfeedAdminbarTools_Mustache_Parser
{
    private $lineNum;
    private $lineTokens;
    private $pragmas;
    private $defaultPragmas = array();

    private $pragmaFilters;
    private $pragmaBlocks;

    /**
     * Process an array of Mustache tokens and convert them into a parse tree.
     *
     * @param array $tokens Set of Mustache tokens
     *
     * @return array Mustache token parse tree
     */
    public function parse(array $tokens = array())
    {
        $this->lineNum    = -1;
        $this->lineTokens = 0;
        $this->pragmas    = $this->defaultPragmas;

        $this->pragmaFilters = isset($this->pragmas[ScreenfeedAdminbarTools_Mustache_Engine::PRAGMA_FILTERS]);
        $this->pragmaBlocks  = isset($this->pragmas[ScreenfeedAdminbarTools_Mustache_Engine::PRAGMA_BLOCKS]);

        return $this->buildTree($tokens);
    }

    /**
     * Enable pragmas across all templates, regardless of the presence of pragma
     * tags in the individual templates.
     *
     * @internal Users should set global pragmas in ScreenfeedAdminbarTools_Mustache_Engine, not here :)
     *
     * @param string[] $pragmas
     */
    public function setPragmas(array $pragmas)
    {
        $this->pragmas = array();
        foreach ($pragmas as $pragma) {
            $this->enablePragma($pragma);
        }
        $this->defaultPragmas = $this->pragmas;
    }

    /**
     * Helper method for recursively building a parse tree.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException when nesting errors or mismatched section tags are encountered
     *
     * @param array &$tokens Set of Mustache tokens
     * @param array $parent  Parent token (default: null)
     *
     * @return array Mustache Token parse tree
     */
    private function buildTree(array &$tokens, array $parent = null)
    {
        $nodes = array();

        while (!empty($tokens)) {
            $token = array_shift($tokens);

            if ($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE] === $this->lineNum) {
                $this->lineTokens++;
            } else {
                $this->lineNum    = $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE];
                $this->lineTokens = 0;
            }

            if ($this->pragmaFilters && isset($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME])) {
                list($ScreenfeedAdminbarTools_name, $filters) = $this->getNameAndFilters($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME]);
                if (!empty($filters)) {
                    $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME]    = $ScreenfeedAdminbarTools_name;
                    $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::FILTERS] = $filters;
                }
            }

            switch ($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE]) {
                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_DELIM_CHANGE:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    break;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_SECTION:
                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_INVERTED:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_END_SECTION:
                    if (!isset($parent)) {
                        $msg = sprintf(
                            'Unexpected closing tag: /%s on line %d',
                            $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME],
                            $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE]
                        );
                        throw new ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException($msg, $token);
                    }

                    if ($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME] !== $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME]) {
                        $msg = sprintf(
                            'Nesting error: %s (on line %d) vs. %s (on line %d)',
                            $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME],
                            $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE],
                            $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME],
                            $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE]
                        );
                        throw new ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException($msg, $token);
                    }

                    $this->clearStandaloneLines($nodes, $tokens);
                    $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::END]   = $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::INDEX];
                    $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::NODES] = $nodes;

                    return $parent;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_PARTIAL:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    //store the whitespace ScreenfeedAdminbarTools_prefix for laters!
                    if ($indent = $this->clearStandaloneLines($nodes, $tokens)) {
                        $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::INDENT] = $indent[ScreenfeedAdminbarTools_Mustache_Tokenizer::VALUE];
                    }
                    $nodes[] = $token;
                    break;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_PARENT:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_BLOCK_VAR:
                    if ($this->pragmaBlocks) {
                        // BLOCKS pragma ScreenfeedAdminbarTools_is enabled, let's do this!
                        if (isset($parent) && $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE] === ScreenfeedAdminbarTools_Mustache_Tokenizer::T_PARENT) {
                            $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE] = ScreenfeedAdminbarTools_Mustache_Tokenizer::T_BLOCK_ARG;
                        }
                        $this->clearStandaloneLines($nodes, $tokens);
                        $nodes[] = $this->buildTree($tokens, $token);
                    } else {
                        // pretend this was just a normal "escaped" token...
                        $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE] = ScreenfeedAdminbarTools_Mustache_Tokenizer::T_ESCAPED;
                        // TODO: figure out how to figure out if there was a space after this dollar:
                        $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME] = '$' . $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME];
                        $nodes[] = $token;
                    }
                    break;

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_PRAGMA:
                    $this->enablePragma($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME]);
                    // no break

                case ScreenfeedAdminbarTools_Mustache_Tokenizer::T_COMMENT:
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $token;
                    break;

                default:
                    $nodes[] = $token;
                    break;
            }
        }

        if (isset($parent)) {
            $msg = sprintf(
                'Missing closing tag: %s opened on line %d',
                $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::NAME],
                $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE]
            );
            throw new ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException($msg, $parent);
        }

        return $nodes;
    }

    /**
     * Clear standalone line tokens.
     *
     * Returns a whitespace token for indenting partials, if applicable.
     *
     * @param array $nodes  Parsed nodes
     * @param array $tokens Tokens to be parsed
     *
     * @return array|null Resulting indent token, if any
     */
    private function clearStandaloneLines(array &$nodes, array &$tokens)
    {
        if ($this->lineTokens > 1) {
            // this ScreenfeedAdminbarTools_is the third or later node on this line, so it can't be standalone
            return;
        }

        $prev = null;
        if ($this->lineTokens === 1) {
            // this ScreenfeedAdminbarTools_is the second node on this line, so it can't be standalone
            // unless the previous node ScreenfeedAdminbarTools_is whitespace.
            if ($prev = end($nodes)) {
                if (!$this->tokenIsWhitespace($prev)) {
                    return;
                }
            }
        }

        if ($next = reset($tokens)) {
            // If we're on a new line, bail.
            if ($next[ScreenfeedAdminbarTools_Mustache_Tokenizer::LINE] !== $this->lineNum) {
                return;
            }

            // If the next token isn't whitespace, bail.
            if (!$this->tokenIsWhitespace($next)) {
                return;
            }

            if (count($tokens) !== 1) {
                // Unless it's the last token in the template, the next token
                // must end in newline for this to be standalone.
                if (substr($next[ScreenfeedAdminbarTools_Mustache_Tokenizer::VALUE], -1) !== "\n") {
                    return;
                }
            }

            // Discard the whitespace suffix
            array_shift($tokens);
        }

        if ($prev) {
            // Return the whitespace ScreenfeedAdminbarTools_prefix, if any
            return array_pop($nodes);
        }
    }

    /**
     * Check whether token ScreenfeedAdminbarTools_is a whitespace token.
     *
     * True if token type ScreenfeedAdminbarTools_is T_TEXT and value ScreenfeedAdminbarTools_is all whitespace characters.
     *
     * @param array $token
     *
     * @return bool True if token ScreenfeedAdminbarTools_is a whitespace token
     */
    private function tokenIsWhitespace(array $token)
    {
        if ($token[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE] === ScreenfeedAdminbarTools_Mustache_Tokenizer::T_TEXT) {
            return preg_match('/^\s*$/', $token[ScreenfeedAdminbarTools_Mustache_Tokenizer::VALUE]);
        }

        return false;
    }

    /**
     * Check whether a token ScreenfeedAdminbarTools_is allowed inside a parent tag.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException if an invalid token ScreenfeedAdminbarTools_is found inside a parent tag
     *
     * @param array|null $parent
     * @param array      $token
     */
    private function checkIfTokenIsAllowedInParent($parent, array $token)
    {
        if (isset($parent) && $parent[ScreenfeedAdminbarTools_Mustache_Tokenizer::TYPE] === ScreenfeedAdminbarTools_Mustache_Tokenizer::T_PARENT) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_SyntaxException('Illegal content in < parent tag', $token);
        }
    }

    /**
     * Split a tag ScreenfeedAdminbarTools_name into ScreenfeedAdminbarTools_name and filters.
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return array [Tag ScreenfeedAdminbarTools_name, Array of filters]
     */
    private function getNameAndFilters($ScreenfeedAdminbarTools_name)
    {
        $filters = array_map('trim', explode('|', $ScreenfeedAdminbarTools_name));
        $ScreenfeedAdminbarTools_name    = array_shift($filters);

        return array($ScreenfeedAdminbarTools_name, $filters);
    }

    /**
     * Enable a pragma.
     *
     * @param string $ScreenfeedAdminbarTools_name
     */
    private function enablePragma($ScreenfeedAdminbarTools_name)
    {
        $this->pragmas[$ScreenfeedAdminbarTools_name] = true;

        switch ($ScreenfeedAdminbarTools_name) {
            case ScreenfeedAdminbarTools_Mustache_Engine::PRAGMA_BLOCKS:
                $this->pragmaBlocks = true;
                break;

            case ScreenfeedAdminbarTools_Mustache_Engine::PRAGMA_FILTERS:
                $this->pragmaFilters = true;
                break;
        }
    }
}
