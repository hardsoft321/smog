<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\view;

use smog\module\Bean;

class Detail extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/views/view.detail.php", function() use ($module) {
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
require_once 'include/MVC/View/views/view.detail.php';

class {$module['name']}ViewDetail extends ViewDetail
{
    public function display()
    {
        parent::display();
    }
}

PHP;
        });
    }
}
