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
        $typolessAddress = preg_replace('/ +/', ' ', $typolessAddress);

        $streetType = $this->getStreetType($typolessAddress);
        if(!in_array(mb_strtolower($streetType), $this->streetTypes)) {
            throw new AddressException("Unknown address format: $address");
        }

        if(preg_match('/(.*?) '.str_replace('.', '\.', $streetType).' (.*)/i', $typolessAddress, $matches)) {
            list($x, $preStreetType, $postStreetType) = $matches;
        }

        $preStreetType  = trim($preStreetType ?? '');
        $postStreetType = trim($postStreetType ?? '');

        if(preg_match('/([0-9]{4} )?([^,]+, )?(.*)/', $preStreetType, $matches)) {
            list($x, $zip, $city, $street) = $matches;
        }

        if(preg_match('/([0-9]+)?(.*)?/', $postStreetType, $matches)) {
            list($x, $houseNumber, $houseExtension) = $matches;
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

    private function getStreetType(string $address): string {
        $addressParts = explode(' ', $address);

        $streetTypePos = 0;
        while(!in_array(mb_strtolower($addressParts[$streetTypePos]), $this->streetTypes) && $streetTypePos++ < count($addressParts) - 1);

        if(isset($addressParts[$streetTypePos + 1]) && !preg_match('/[0-9](.*)/', $addressParts[$streetTypePos + 1])) {
            for($i = $streetTypePos + 1; $i < count($addressParts) - 1; $i++) {
                if(in_array(mb_strtolower($addressParts[$i]), $this->streetTypes) && preg_match('/[0-9](.*)/', $addressParts[$i + 1])) {
                    $streetTypePos = $i;
                    break;
                }
            }
        }

        return $addressParts[$streetTypePos] ?? '';
    }
}
