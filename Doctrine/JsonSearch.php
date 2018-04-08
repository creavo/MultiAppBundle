<?php

namespace Creavo\MultiAppBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonSearch extends FunctionNode {

    public $searchMode;
    public $field;
    public $searchTerm;
    public $escapeChar;
    public $jsonPaths = [];

    public function getSql(SqlWalker $sqlWalker){
        $jsonDoc = $sqlWalker->walkStringPrimary($this->field);
        $mode = $sqlWalker->walkStringPrimary($this->searchMode);
        $searchArgs = $sqlWalker->walkStringPrimary($this->searchTerm);
        if (!empty($this->escapeChar)) {
            $searchArgs .= ', ' . $sqlWalker->walkStringPrimary($this->escapeChar);
            if (!empty($this->jsonPaths)) {
                $jsonPaths = array();
                foreach ($this->jsonPaths as $path) {
                    $jsonPaths[] = $sqlWalker->walkStringPrimary($path);
                }
                $searchArgs .= ', ' . implode(', ', $jsonPaths);
            }
        }
        return sprintf('%s(%s, %s, %s)', 'JSON_SEARCH', $jsonDoc, $mode, $searchArgs);
    }

    public function parse(Parser $parser){
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->searchMode = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->searchTerm = $parser->StringPrimary();
        if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->escapeChar = $parser->StringPrimary();
            while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
                $parser->match(Lexer::T_COMMA);
                $this->jsonPaths[] = $parser->StringPrimary();
            }
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}