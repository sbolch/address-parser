<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit11d0c4c66aadcda73a582766c6890943
{
    public static $prefixLengthsPsr4 = array (
        'd' => 
        array (
            'd3vy\\AddressParser\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'd3vy\\AddressParser\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit11d0c4c66aadcda73a582766c6890943::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit11d0c4c66aadcda73a582766c6890943::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}