<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 07/12/2014
 * Time: 12:21
 */

namespace Mykees\TagBundle\Util;


class Reflection {

    public static function getClassName($model)
    {
        $reflection = new \ReflectionClass($model);
        if(self::isProxyClass($reflection) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }
        return $reflection->getName();
    }

    public static function getClassShortName ( $model ) {
        $reflection = new \ReflectionClass( $model );
        return $reflection->getShortName();
    }

    public static function getBundlePath ( $model  ){
        $explode = explode('\\', self::getClassName($model ));
        return $explode[0].'\\'.$explode[1].'\\'.$explode[2].'\\'.$explode[3];
    }

    public static function getBundleRepository ( $model  ){
        $explode = explode('\\', self::getClassName($model ));
        return $explode[0].$explode[1].':'.$explode[3];
    }

}
