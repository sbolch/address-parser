<?php

namespace d3vy\AddressParser;

interface ParserInterface {
    public function parse(string $address): array;
}
