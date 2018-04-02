<?php

namespace Creavo\MultiAppBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * JSON_EXTRACT(field, path)
 *
 * Class JsonExtractFunction
 * @package Creavo\MultiAppBundle\Doctrine
 */
class JsonExtractFunction extends FunctionNode {

    protected $field;
    protected $path;

    public function parse(Parser $parser){

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->path = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker) {
        return 'JSON_EXTRACT('.$this->field->dispatch($sqlWalker).', '.$this->field->dispatch($sqlWalker).')';
    }

}