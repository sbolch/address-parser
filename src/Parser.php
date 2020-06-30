<?php

namespace d3vy\AddressParser;

class Parser {
    private $country;

    private $streetTypes;

    public function __construct($country) {
        $this->country     = $country;
        $this->streetTypes = StreetTypesGetter::$country();
    }

    public function parse($address) {
        $country = strtolower($this->country);
        return AddressParser::$country($address, $this->streetTypes);
    }
}
