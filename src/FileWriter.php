<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog;

use Zend\Code\Generator\ValueGenerator;

class FileWriter
{
    public static function getCopyright()
    {
        return file_get_contents(SMOG_ROOT . '/src/copyright.txt');
    }

    public static function writeFileIfNotExists(string $path, $contents)
    {
        if (file_exists($path)) {
            echo 'File ' . self::pathToDisplay($path) . ' already exists' . PHP_EOL;
            return false;
        }
        if (!\is_string($contents)) {
            $contents = call_user_func($contents);
        }
        $dirPath = \dirname($path);
        if (!\is_dir($dirPath)) {
            $d = mkdir($dirPath, 0775, true);
            if (!$d) {
                throw new \Exception("Can't create directory {$dirPath}");
            }
            echo 'New directory created: ' . self::pathToDisplay($dirPath) . PHP_EOL;
        }
        $f = file_put_contents($path, $contents);
        if ($f === false) {
            throw new \Exception("Can't create file " . $path);
        }
        echo 'New file created: ' . self::pathToDisplay($path) . PHP_EOL;
        return $f;
    }

    public static function pathToDisplay($path)
    {
        return !empty($GLOBALS['sugar_root'])
            ? ltrim(mb_substr($path, mb_strlen($GLOBALS['sugar_root'])), '/\\')
            : $path;
    }

    public static function varExport($var)
    {
        $generator = new ValueGenerator($var, ValueGenerator::TYPE_ARRAY_LONG);
        $generator->setIndentation('    ');
        return $generator->generate();
    }

    public static function create($options)
    {
        if (empty($options['file'])) {
            throw new ArgumentException('Undefined file name. Use --file option.');
        }
        self::writeFileIfNotExists($options['file'], function() {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
PHP;
        });
    }
}
