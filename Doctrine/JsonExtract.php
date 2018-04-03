<?php

namespace Creavo\MultiAppBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * JSON_EXTRACT(field, paths)
 *
 * Class JsonExtractFunction
 * @package Creavo\MultiAppBundle\Doctrine
 */
class JsonExtract extends FunctionNode {

    public $field;
    public $paths = [];

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $jsonDoc = $sqlWalker->walkStringPrimary($this->field);
        $paths = array();
        foreach ($this->paths as $path) {
            $paths[] = $sqlWalker->walkStringPrimary($path);
        }

        return sprintf('%s(%s, %s)', 'JSON_EXTRACT', $jsonDoc, implode(', ', $paths));
    }

    /**
     * @param Parser $parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->paths[] = $parser->StringPrimary();
        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->paths[] = $parser->StringPrimary();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}