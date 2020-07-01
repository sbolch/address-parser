<?php

namespace d3vy\AddressParser\Parser;

use d3vy\AddressParser\ParserInterface;
use d3vy\AddressParser\Exception\AddressException;

class HUParser implements ParserInterface {
    private $frequentTypos;
    private $streetTypes;

    public function __construct() {
        $data = simplexml_load_file('https://httpmegosztas.posta.hu/PartnerExtra/Out/StreetTypes.xml');

        $this->streetTypes = [];
        foreach(json_decode(json_encode($data), true)['streetType'] as $type) {
            if(is_string($type)) {
                $this->streetTypes[] = mb_strtolower($type);
            }
        }
        $this->streetTypes = array_merge($this->streetTypes, [
            'hrsz.', 'hrsz',
            'krt.', 'krt',
            'ltp.', 'ltp',
            'rkp.', 'rkp',
            'sgrt.', 'sgrt',
            'sgt.', 'sgt',
            'stny.', 'stny',
            'st.',
            'u.'
        ]);

        $this->streetTypes   = array_reverse($this->streetTypes);
        $this->frequentTypos = json_decode(file_get_contents(dirname(__DIR__).'/../locales/hu/frequent-typos.json'));
    }

    public function parse(string $address): array {
        $typolessAddress = $address;
        foreach($this->frequentTypos as $right => $wrongs) {
            foreach($wrongs as $wrong) {
                $typolessAddress = str_replace($wrong, $right, $typolessAddress);
                $typolessAddress = str_replace(ucwords($wrong), $right, $typolessAddress);
                $typolessAddress = str_replace(mb_strtoupper($wrong), $right, $typolessAddress);
                if($typolessAddress !== $address) {
                    break;
                }
            }
        }

        $addressParts = explode(' ', $typolessAddress);

        $streetTypePos = 0;
        while(!in_array(mb_strtolower($addressParts[$streetTypePos]), $this->streetTypes) && $streetTypePos++ < count($addressParts) - 1);
        while(isset($addressParts[$streetTypePos + 1]) && in_array(mb_strtolower($addressParts[$streetTypePos + 1]), $this->streetTypes)) {
            $streetTypePos++;
        }

        $streetType = $addressParts[$streetTypePos] ?? '';
        if(!in_array(mb_strtolower($streetType), $this->streetTypes)) {
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
            'zip'             => $zip ?? '',
            'city'            => trim(str_replace(',', '', $city ?? '')),
            'street'          => $street ?? '',
            'streetType'      => $streetType,
            'houseNumber'     => trim($houseNumber ?? ''),
            'houseNumberInfo' => trim(ltrim($houseExtension ?? '', '.,'))
        ];
    }
}
