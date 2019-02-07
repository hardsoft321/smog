<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\view;

use smog\module\Bean;

class Edit extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/views/view.edit.php", function() use ($module) {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
require_once 'include/MVC/View/views/view.edit.php';

class {$module['name']}ViewEdit extends ViewEdit
{
    public \$useForSubpanel = true;

    public function display()
    {
        parent::display();
    }
}

PHP;
        });
    }
}
