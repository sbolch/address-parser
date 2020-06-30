<?php

namespace d3vy\AddressParser;

class AddressParser {

    public static function hu($address, $streetTypes) {
        $frequentTypos = json_decode(file_get_contents(dirname(__DIR__).'/countries/hu/frequent-typos.json'));

        $typolessAddress = $address;
        foreach($frequentTypos as $right => $wrongs) {
            foreach($wrongs as $wrong) {
                $typolessAddress = str_replace($wrong, $right, $typolessAddress);
                $typolessAddress = str_replace(ucwords($wrong), $right, $typolessAddress);
                $typolessAddress = str_replace(mb_strtoupper($wrong), $right, $typolessAddress);
            }
        }

        $addressParts = explode(' ', $typolessAddress);

        $streetTypePos = 0;
        while(!in_array(mb_strtolower($addressParts[$streetTypePos]), $streetTypes) && $streetTypePos++ < count($addressParts) - 1);
        while(isset($addressParts[$streetTypePos + 1]) && in_array(mb_strtolower($addressParts[$streetTypePos + 1]), $streetTypes)) {
            $streetTypePos++;
        }

        $streetType = $addressParts[$streetTypePos] ?? '';
        if(!in_array(mb_strtolower($streetType), $streetTypes)) {
            throw new AddressException("Unknown address format: $address");
        }

        $streetTypeUcfirst = ucfirst($streetType);
        $streetTypeUc      = mb_strtoupper($streetType);

        if(preg_match('/(.*)('.str_replace('.', '\.', "$streetType|$streetTypeUcfirst|$streetTypeUc").')(.*)/', $typolessAddress, $matches)) {
            list($a, $preStreetType, $b, $postStreetType) = $matches;
        }

        $preStreetType  = trim($preStreetType);
        $postStreetType = trim($postStreetType);

        if(preg_match('/([0-9]{4} )?([^,]+, )?(.*)/', $preStreetType, $matches)) {
            list($a, $zip, $city, $street) = $matches;
        }

        if(preg_match('/([0-9]+)?(.*)?/', $postStreetType, $matches)) {
            list($a, $houseNumber, $houseExtension) = $matches;
        }

        return [
            'zip'            => $zip ?? '',
            'city'           => isset($city) && $city ? trim(str_replace(',', '', $city)) : '',
            'street'         => $street ?? '',
            'streetType'     => $streetType,
            'houseNumber'    => isset($houseNumber) && $houseNumber ? trim($houseNumber) : '',
            'houseExtension' => isset($houseExtension) && $houseExtension ? trim(ltrim($houseExtension, '.,')) : ''
        ];
    }
}
