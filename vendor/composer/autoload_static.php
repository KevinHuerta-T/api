<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb609f9dade12b78c71d2cc5b83f853a4
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb609f9dade12b78c71d2cc5b83f853a4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb609f9dade12b78c71d2cc5b83f853a4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb609f9dade12b78c71d2cc5b83f853a4::$classMap;

        }, null, ClassLoader::class);
    }
}
