<?php

namespace sbolch\AddressParser;

interface ParserInterface {
    public function parse(string $address): array;
}
