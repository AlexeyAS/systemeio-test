<?php

namespace App\Enum;

use App\Enum\CountryEnum;

class TaxEnum extends BaseEnum
{
    const TAX_NUMBER_PATTERN = [
        CountryEnum::COUNTRY_CODE_GERMANY => '/' . CountryEnum::COUNTRY_CODE_GERMANY . '\d{9}/',
        CountryEnum::COUNTRY_CODE_GREECE => '/' . CountryEnum::COUNTRY_CODE_GREECE . '\d{9}/',
        CountryEnum::COUNTRY_CODE_ITALY => '/' . CountryEnum::COUNTRY_CODE_ITALY . '\d{11}/',
        CountryEnum::COUNTRY_CODE_FRANCE => '/' . CountryEnum::COUNTRY_CODE_FRANCE . '[a-zA-Z]{2}\d{9}/'
    ];
    const TAX_NUMBER_MESSAGE = [
        CountryEnum::COUNTRY_CODE_GERMANY => CountryEnum::COUNTRY_CODE_GERMANY . 'XXXXXXXXX' . '(9 digits)',
        CountryEnum::COUNTRY_CODE_GREECE => CountryEnum::COUNTRY_CODE_GREECE . 'XXXXXXXXX' . '(9 digits)',
        CountryEnum::COUNTRY_CODE_ITALY => CountryEnum::COUNTRY_CODE_ITALY . 'XXXXXXXXXXX' . '(11 digits)',
        CountryEnum::COUNTRY_CODE_FRANCE => CountryEnum::COUNTRY_CODE_FRANCE . 'YYXXXXXXXXX' . '(2 letters, 9 digits)'
    ];
}