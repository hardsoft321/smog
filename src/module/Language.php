<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

class Language extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        if (empty($GLOBALS['sugar_config']['languages'])) {
            echo '$GLOBALS[\'sugar_config\'][\'languages\'] is empty' . PHP_EOL;
            return;
        }
        $dictionary = [];
        include "{$module['path']}/vardefs.php";
        $langs = array_keys($GLOBALS['sugar_config']['languages']);
        foreach ($langs as $lang) {
            self::writeFileIfNotExists("{$module['path']}/language/{$lang}.lang.php", function() use ($module, $dictionary) {
                $copyright = self::getCopyright();
                $mod_strings = [
                    'LBL_MODULE_NAME' => $module['name'],
                    'LBL_' . \strtoupper($module['name']) => $module['name'],
                    'LBL_OBJECT_NAME' => $module['object_name'] ?? $module['name'],
                    'LNK_NEW_RECORD' => 'New Record',
                    'LNK_LIST' => 'List',
                ];
                if (!empty($dictionary[$module['object_name']])) {
                    foreach ($dictionary[$module['object_name']]['fields'] as $field) {
                        if ($field['name'] == 'currency_id') {
                            continue;
                        }
                        $mod_strings[$field['vname']] = \ucwords(\str_replace('_', ' ', $field['name']));
                    }
                }
                $mod_stringsString = self::varExport($mod_strings);
                return <<<PHP
<?php
{$copyright}
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

\$mod_strings = $mod_stringsString;

PHP;
            });
        }
    }
}
