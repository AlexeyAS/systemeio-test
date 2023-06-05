<?php
namespace App\Enum;

use ReflectionClass;
use ReflectionException;


abstract class BaseEnum {
    private static ?array $constCacheArray = NULL;

    /**
     * @throws ReflectionException
     */
    public static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }
}