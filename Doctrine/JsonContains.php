<?php

namespace Creavo\MultiAppBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonContains extends FunctionNode {


    public $field;
    public $value;
    public $path;

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $jsonDoc = $sqlWalker->walkStringPrimary($this->field);
        $jsonVal = $sqlWalker->walkStringPrimary($this->value);
        $jsonPath = '';
        if (!empty($this->path)) {
            $jsonPath = ', ' . $sqlWalker->walkStringPrimary($this->path);
        }

        return sprintf('%s(%s, %s)', 'JSON_CONTAINS', $jsonDoc, $jsonVal . $jsonPath);
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
        $this->value = $parser->StringPrimary();
        if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->path = $parser->StringPrimary();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}