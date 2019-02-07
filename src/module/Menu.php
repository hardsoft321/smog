<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

class Menu extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/Menu.php", function() use ($module) {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

if (ACLController::checkAccess('{$module['name']}', 'edit', true)) {
    \$module_menu[] = array("index.php?module={$module['name']}&action=EditView", translate('LNK_NEW_RECORD', '{$module['name']}'), '{$module['name']}');
}

if (ACLController::checkAccess('{$module['name']}', 'list', true)) {
    \$module_menu[] = array("index.php?module={$module['name']}&action=index", translate('LNK_LIST', '{$module['name']}'), '{$module['name']}');
}

PHP;
        });
    }
}
