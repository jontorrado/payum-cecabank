<?php
namespace JonTorrado\Payum\Cecabank\Helper;

class LanguageCode
{
    private static $languageCodes = array(
        'es' => 1,
        'ca' => 2,
        'eu' => 3,
        'ga' => 4,
        'va' => 5,
        'en' => 6,
        'fr' => 7,
        'de' => 8,
        'pr' => 9,
        'it' => 10,
        'ru' => 11,
        'no' => 12
    );

    /**
     * Gets the language code of a given locale.
     *
     * @param $locale
     *
     * @return int|null
     */
    public static function getCode($locale)
    {
        return isset(self::$languageCodes[$locale])
            ? self::$languageCodes[$locale]
            : null;
    }
}
