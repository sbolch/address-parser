<?php

namespace d3vy\AddressParser;

class Parser {
    public $country;

    public function parse($address) {
        $country = strtolower($this->country);
        return AddressParser::$country($address);
    }
}
