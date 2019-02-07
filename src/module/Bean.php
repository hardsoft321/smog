<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

use smog\ArgumentException;
use smog\AppInclude;
use smog\FileWriter;
use smog\Module;
use smog\ModuleInstaller;
use smog\SugarRootDir;

class Bean extends FileWriter
{
    private static $module;

    public static function create($options)
    {
        $module = self::getModule($options);
        if (empty($module['object_name'])) {
            throw new ArgumentException('Undefined object name. Use --object option.');
        }
        if (empty($module['table_name'])) {
            throw new ArgumentException('Undefined table name. Use --table option.');
        }
        self::writeFileIfNotExists("{$module['path']}/{$module['object_name']}.php", function() use ($module) {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
class {$module['object_name']} extends Basic
{
    public \$module_dir = '{$module['name']}';
    public \$object_name = '{$module['object_name']}';
    public \$table_name = '{$module['table_name']}';

    public function bean_implements(\$interface)
    {
        switch(\$interface) {
            case 'ACL':return true;
        }
        return false;
    }
}

PHP;
        });

        $f = self::writeFileIfNotExists(
            "{$module['root']}/custom/Extension/application/Ext/Include/{$module['name']}.php"
            , function() use ($module, $options) {
            $invis = isset($options['invis']) ? (bool)$options['invis'] : false;
            $objectFile = ltrim(mb_substr("{$module['path']}/{$module['object_name']}.php", mb_strlen($module['root'])), '/\\');
            $copyright = self::getCopyright();
            $moduleList = $invis ? 'modInvisList' : 'moduleList';
            return <<<PHP
<?php
{$copyright}
\$beanList['{$module['name']}'] = '{$module['object_name']}';
\$beanFiles['{$module['object_name']}'] = '{$objectFile}';
\${$moduleList}[] = '{$module['name']}';

PHP;
        });
        if ($f !== false) {
            self::rebuildModules($module['root']);
        }

        if (!empty($GLOBALS['sugar_config']['languages'])) {
            $langs = array_keys($GLOBALS['sugar_config']['languages']);
            foreach ($langs as $lang) {
                self::writeFileIfNotExists(
                    "{$module['root']}/custom/Extension/application/Ext/Language/{$lang}.{$module['name']}.php"
                    , function() use ($module) {
                    $copyright = self::getCopyright();
                    return <<<PHP
<?php
{$copyright}
\$app_list_strings['moduleList']['{$module['name']}'] = '{$module['name']}';

PHP;
                });
            }
        }
    }

    protected static function getModule($options)
    {
        $cwd = !empty($GLOBALS['CWD']) ? $GLOBALS['CWD'] : getcwd();
        SugarRootDir::chdirToRoot();
        SugarRootDir::includeEntryFiles();
        $root = getcwd();
        $moduleName = null;
        if (!empty($options['module'])) {
            $moduleName = $options['module'];
        } else {
            $relPath = mb_substr($cwd, mb_strlen($root));
            $relPath = trim($relPath, '/\\');
            $relPathDirs = explode(DIRECTORY_SEPARATOR, $relPath);
            if (count($relPathDirs) >= 2 && $relPathDirs[0] == 'modules') {
                $moduleName = $relPathDirs[1];
            }
        }
        if (empty($moduleName)) {
            throw new ArgumentException('Undefined module. Change directory to some module or use --module option.');
        }
        $modulePath = "{$root}/modules/{$moduleName}";

        $objectName = null;
        if (!empty($options['object'])) {
            $objectName = $options['object'];
        }
        elseif (!empty($GLOBALS['beanList'][$moduleName])) {
            $objectName = self::getObjectNameForModuleName($moduleName);
        }

        $tableName = null;
        if (!empty($options['table'])) {
            $tableName = $options['table'];
        }
        else {
            $tableName = self::getTableNameForModule([
                'path' => $modulePath,
                'object_name' => $objectName,
            ]);
        }

        return [
            'name' => $moduleName,
            'path' => $modulePath,
            'root' => $root,
            'object_name' => $objectName,
            'table_name' => $tableName,
        ];
    }

    public static function getObjectNameForModuleName($moduleName)
    {
        return $GLOBALS['beanList'][$moduleName] ?? '';
    }

    public static function getTableNameForModule(array $module)
    {
        if (empty($module['object_name'])) {
            return '';
        }
        if (empty($module['path'])) {
            $module['path'] = "{$module['root']}/modules/{$module['name']}";
        }
        $objectPath = "{$module['path']}/{$module['object_name']}.php";
        if (!\file_exists($objectPath)) {
            return '';
        }
        require_once $objectPath;
        $reflectionClass = new \ReflectionClass($module['object_name']);
        $defaultProperties = $reflectionClass->getDefaultProperties();
        return $defaultProperties['table_name'];
    }

    public static function rebuildModules($root)
    {
        $path = $root . '/custom/application/Ext/Include/modules.ext.php';
        if (file_exists($path)) {
            $d = unlink($path);
            if ($d) {
                echo 'Autogenerated file deleted: ' . self::pathToDisplay($path) . PHP_EOL;
            }
            else {
                echo 'Can\'t delete file: ' . $path . PHP_EOL;
            }
        }
        else {
            echo 'File not exists (Is it OK?): ' , $path , PHP_EOL;
        }
        $mi = new ModuleInstaller();
        $mi->rebuild_modules();
        echo PHP_EOL;
    }
}
