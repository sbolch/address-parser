<?php

namespace d3vy\AddressParser;

class StreetTypesGetter {

    public static function hu() {
        $data = simplexml_load_file('https://httpmegosztas.posta.hu/PartnerExtra/Out/StreetTypes.xml');

        $streetTypes = [];
        foreach(json_decode(json_encode($data), true)['streetType'] as $type) {
            if(is_string($type)) {
                $streetTypes[] = mb_strtolower($type);
            }
        }
        $streetTypes = array_merge($streetTypes, [
            'hrsz.', 'hrsz',
            'krt.', 'krt',
            'ltp.', 'ltp',
            'rkp.', 'rkp',
            'sgt.', 'sgt',
            'stny.', 'stny',
            'st.',
            'u.'
        ]);

        return array_reverse($streetTypes);
    }
}
