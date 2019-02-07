<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

class Controller extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/controller.php", function() use ($module) {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
class {$module['name']}Controller extends SugarController
{
}

PHP;
        });
    }
}
