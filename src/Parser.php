<?php

namespace d3vy\AddressParser;

class Parser {
    private $parser;

    public function __construct($locale) {
        $parserClass = "d3vy\AddressParser\Parser\\{$locale}Parser";
        $this->parser = new $parserClass;
    }

    public function parse($address): array {
        return $this->parser->parse((string)$address);
    }
}
