<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog;

class ModuleInstaller extends \ModuleInstaller
{
    public function __construct()
    {
        // here we skip db connecting
        include("ModuleInstall/extensions.php");
        $this->extensions = $extensions;
    }
}
