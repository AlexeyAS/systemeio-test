<?php

namespace App\Factory;

use App\Enum\CountryEnum;
use RuntimeException;

class TaxValidatorFactory
{
    /**
     * Получаем правило валидации и сообщение ошибки
     * @param string|null $value
     * @return string[]
     */
    public function create(?string $value = null): array
    {
        return match ($value) {
            CountryEnum::COUNTRY_CODE_GERMANY => [
                'pattern' => '/' . CountryEnum::COUNTRY_CODE_GERMANY . '\d{9}/',
                'message' => CountryEnum::COUNTRY_CODE_GERMANY . 'XXXXXXXXX' . '(9 digits)'
            ],
            CountryEnum::COUNTRY_CODE_GREECE => [
                'pattern' => '/' . CountryEnum::COUNTRY_CODE_GREECE . '\d{9}/',
                'message' => CountryEnum::COUNTRY_CODE_GREECE . 'XXXXXXXXX' . '(9 digits)'
            ],
            CountryEnum::COUNTRY_CODE_ITALY => [
                'pattern' => '/' . CountryEnum::COUNTRY_CODE_ITALY . '\d{11}/',
                'message' => CountryEnum::COUNTRY_CODE_ITALY . 'XXXXXXXXXXX' . '(11 digits)'
            ],
            CountryEnum::COUNTRY_CODE_FRANCE => [
                'pattern' => '/' . CountryEnum::COUNTRY_CODE_FRANCE . '[a-zA-Z]{2}\d{9}/',
                'message' => CountryEnum::COUNTRY_CODE_FRANCE . 'YYXXXXXXXXX' . '(2 letters, 9 digits)'
            ],
            default => throw new RuntimeException("Unknown Country Code")
        };
    }
}