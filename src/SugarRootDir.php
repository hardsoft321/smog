<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog;

class SugarRootDir
{
    private static $login;

    public static function chdirToRoot($path = null)
    {
        if (empty($GLOBALS['CWD'])) {
            $GLOBALS['CWD'] = getcwd();
        }
        if ($path === null) {
            $path = getcwd();
        }
        if (self::isRootDir($path)) {
            if (getcwd() != $path) {
                chdir($path);
            }
            $GLOBALS['sugar_root'] = $path;
            return;
        }
        $parentPath = dirname($path);
        if ($parentPath == $path) {
            throw new SugarRootDirNotFoundException('SugarCRM root dir not found');
        }
        self::chdirToRoot($parentPath);
    }

    public static function isRootDir($path = null)
    {
        if ($path === null) {
            $path = getcwd();
        }
        return file_exists($path . '/sugar_version.php') && file_exists($path . '/include/entryPoint.php');
    }

    /**
     * @author https: //github.com/fayebsg/sugarcrm-cli/blob/master/src/SugarCLI/Commands/QuickRepairRebuild.php
     * @author pea
     */
    public static function includeEntryPoint($login = null)
    {
        global $sugar_config;

        if (!defined('sugarEntry')) {
            define('sugarEntry', true);
        }

        require 'config.php';
        $GLOBALS['sugar_config'] = $sugar_config;
        require_once 'include/entryPoint.php';

        // Scope is messed up due to requiring files within a function
        // We need to explicitly assign these variables to $GLOBALS
        foreach (get_defined_vars() as $key => $val) {
            $GLOBALS[$key] = $val;
        }

        if (empty($current_language)) {
            $current_language = $sugar_config['default_language'];
        }

        $GLOBALS['app_list_strings'] = return_app_list_strings_language($current_language);
        $GLOBALS['app_strings'] = return_application_language($current_language);
        $GLOBALS['mod_strings'] = array_merge(
            return_module_language($current_language, "Administration"),
            return_module_language($current_language, "UpgradeWizard")
        );

        global $current_user;
        $current_user = new \User();
        if (!empty($login)) {
            $current_user->retrieve_by_string_fields(array(
                'user_name' => $login,
            ));
        } else {
            $current_user->getSystemUser();
        }
        if (empty($current_user->id)) {
            fwrite(STDERR, 'Warning: User not found' . PHP_EOL);
        }
        self::$login = $current_user->user_name;

        if (\UploadStream::getSuhosinStatus() == false) {
            fwrite(STDERR, 'Warning: ' . htmlspecialchars_decode($GLOBALS['app_strings']['ERR_SUHOSIN']) . PHP_EOL);
        }
    }

    public static function includeEntryFiles()
    {
        global $sugar_config;

        if (!defined('sugarEntry')) {
            define('sugarEntry', true);
        }

        require 'config.php';
        $GLOBALS['sugar_config'] = $sugar_config;

        require_once('include/utils.php');
        require_once('include/utils/file_utils.php');
        require_once('ModuleInstall/ModuleInstaller.php');
        require_once('include/SugarLogger/LoggerManager.php');
        include('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        require_once('data/SugarBean.php');
        include_once('include/SugarObjects/templates/basic/Basic.php');
        require_once('include/SugarObjects/LanguageManager.php');
        require_once('include/SugarObjects/VardefManager.php');
        require_once('include/SugarCache/SugarCache.php');
        $GLOBALS['log'] = \LoggerManager::getLogger('SugarCRM');
    }

    public static function getLogin()
    {
        return self::$login;
    }
}
