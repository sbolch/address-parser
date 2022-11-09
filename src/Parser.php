<?php

namespace sbolch\AddressParser;

class Parser {
    private $parser;

    public function __construct($locale) {
        $parserClass = "sbolch\AddressParser\Parser\\{$locale}Parser";
        $this->parser = new $parserClass;
    }

    public function parse($address): array {
        return $this->parser->parse((string)$address);
    }
}
