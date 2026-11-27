<?php

declare(strict_types=1);

namespace kim\present\lifespan\tests;

use PHPUnit\Framework\TestCase;
use pocketmine\plugin\PluginBase;

/**
 * Checks that plugin.yml and composer.json agree with each other and with the source tree, since the plugin is
 * loaded by the server through plugin.yml only.
 */
final class PluginManifestTest extends TestCase{

    /** @return array<string, string> top-level scalar entries of plugin.yml */
    private static function manifest() : array{
        $entries = [];
        foreach(file(dirname(__DIR__) . '/plugin.yml', FILE_IGNORE_NEW_LINES) ?: [] as $line){
            if(preg_match('/^([a-z-]+):\s*(.*)$/', $line, $m) === 1){
                $entries[$m[1]] = $m[2];
            }
        }
        return $entries;
    }

    public function testMainClassIsAPlugin() : void{
        $main = self::manifest()['main'] ?? '';
        self::assertTrue(class_exists($main), "Main class $main of plugin.yml does not exist");
        self::assertTrue(is_subclass_of($main, PluginBase::class));
    }

    public function testNamespacePrefixMatchesComposerAutoload() : void{
        $prefix = self::manifest()['src-namespace-prefix'] ?? '';
        $composer = json_decode((string) file_get_contents(dirname(__DIR__) . '/composer.json'), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($composer);
        self::assertSame($prefix . '\\', array_key_first($composer['autoload']['psr-4']));
        self::assertSame($prefix . '\\Main', self::manifest()['main']);
    }

    public function testTargetsTheAxolotlApi() : void{
        self::assertSame('[ 5.51.0 ]', self::manifest()['api'] ?? null);
    }
}
