<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0019a98e4ee825407c22b738630641a3
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Scaffolding\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Scaffolding\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static $classMap = array (
        'Scaffolding\\TemplateTags' => __DIR__ . '/../..' . '/lib/TemplateTags.php',
        'Scaffolding\\Templates' => __DIR__ . '/../..' . '/lib/Templates.php',
        'Scaffolding\\Theme' => __DIR__ . '/../..' . '/lib/Theme.php',
        'Scaffolding\\ThemeMods' => __DIR__ . '/../..' . '/lib/ThemeMods.php',
        'Scaffolding\\views\\Archive' => __DIR__ . '/../..' . '/lib/views/Archive.php',
        'Scaffolding\\views\\Blog' => __DIR__ . '/../..' . '/lib/views/Blog.php',
        'Scaffolding\\views\\Frontpage' => __DIR__ . '/../..' . '/lib/views/Frontpage.php',
        'Scaffolding\\views\\Search' => __DIR__ . '/../..' . '/lib/views/Search.php',
        'Scaffolding\\views\\Single' => __DIR__ . '/../..' . '/lib/views/Single.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0019a98e4ee825407c22b738630641a3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0019a98e4ee825407c22b738630641a3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0019a98e4ee825407c22b738630641a3::$classMap;

        }, null, ClassLoader::class);
    }
}
