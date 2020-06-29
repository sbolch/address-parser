<?php

namespace d3vy\AddressParser;

class StreetTypesGetter {

    public static function hu() {
        $data = simplexml_load_file('https://httpmegosztas.posta.hu/PartnerExtra/Out/StreetTypes.xml')
            or die('Error while getting street types');

        $streetTypes = array();
        foreach(json_decode(json_encode($data), true)['streetType'] as $type) {
            if(is_string($type)) {
                $streetTypes[] = $type;
            }
        }

        return $streetTypes;
    }
}
