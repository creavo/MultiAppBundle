<?php

namespace Creavo\MultiAppBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonUnquote extends FunctionNode {

    public $field;

    public function getSql(SqlWalker $sqlWalker) {
        $jsonVal = $sqlWalker->walkStringPrimary($this->field);
        return sprintf('%s(%s)', 'JSON_UNQUOTE', $jsonVal);
    }

    public function parse(Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}