<?php

namespace d3vy\AddressParser;

class AddressParser {

    public static function hu($address) {
        $streetTypes  = StreetTypesGetter::hu();
        $addressArray = explode(' ', $address);

        $streetTypePos = count($addressArray) - 1;
        while(!in_array($addressArray[$streetTypePos], $streetTypes) && $streetTypePos-- > -1);

        $streetType = $addressArray[$streetTypePos];

        $preStreetType = '';
        for($i = 0; $i < $streetTypePos; $i++) {
            $preStreetType .= $addressArray[$i].' ';
        }
        $preStreetType = trim($preStreetType);

        preg_match('/([0-9]{4} )?([^,], )?(.*)/',
            $preStreetType, $matches
        );
        list($a, $zip, $city, $street) = $matches;

        $postStreetType = '';
        for($i = $streetTypePos + 1; $i < count($addressArray); $i ++) {
            $postStreetType .= $addressArray[$i].' ';
        }
        $postStreetType = trim($postStreetType);

        preg_match('/([0-9]+)(.*)/',
            $postStreetType, $matches
        );
        list($a, $houseNumber, $houseExtension) = $matches;

        return array(
            'zip'            => $zip,
            'city'           => $city,
            'street'         => $street,
            'streetType'     => $streetType,
            'houseNumber'    => $houseNumber,
            'houseExtension' => trim(ltrim($houseExtension, '.'))
        );
    }
}
