<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\custom\Extension;

use smog\module\Bean;

class RelatedToModule extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        $extensions = [];
        $dictionary = [];
        include "{$module['path']}/vardefs.php";
        if (!empty($dictionary[$module['object_name']])) {
            foreach ($dictionary[$module['object_name']]['fields'] as $field) {
                if ($field['type'] == 'link') {
                    $extensions[$field['module']]['vardefs'][] = [
                        'type' => 'fields',
                        'key' => $module['name'],
                        'value' => [
                            'name' => $module['name'],
                            'vname' => 'LBL_' . \strtoupper($module['name']),
                            'type' => 'link',
                            'relationship' => $field['relationship'],
                            'module' => $module['name'],
                            'link_type' => 'many',
                            'source' => 'non-db',
                        ],
                    ];
                    $extensions[$field['module']]['layout_defs'][\strtolower($module['name'])] = [
                        'order' => rand(1, 100),
                        'sort_by' => 'name',
                        'sort_order' => 'asc',
                        'module' => $module['name'],
                        'refresh_page' => 0,
                        'subpanel_name' => 'default',
                        'title_key' => 'LBL_' . \strtoupper($module['name']),
                        'get_subpanel_data' => $module['name'],
                        'top_buttons' => [
                            [ 'widget_class' => 'SubPanelTopButtonQuickCreate' ],
                            [ 'widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect' ],
                        ],
                    ];
                }
            }
        }
        foreach ($extensions as $relatedModuleName => $ext) {
            $relatedObjectName = $GLOBALS['beanList'][$relatedModuleName];
            if (!empty($ext['vardefs'])) {
                self::writeFileIfNotExists(
                    "{$module['root']}/custom/Extension/modules/{$relatedModuleName}/Ext/Vardefs/{$module['name']}.php"
                    , function() use ($module, $relatedObjectName, $ext) {
                    $copyright = self::getCopyright();
                    $contents = <<<PHP
<?php
{$copyright}

PHP;

                    foreach ($ext['vardefs'] as $vardef) {
                        $str = self::varExport($vardef['value']);
                        $contents .= <<<PHP
\$dictionary['{$relatedObjectName}']['{$vardef['type']}']['{$vardef['key']}'] = {$str};

PHP;
                    }
                    return $contents;
                });
            }

            if (!empty($ext['layout_defs'])) {
                self::writeFileIfNotExists(
                    "{$module['root']}/custom/Extension/modules/{$relatedModuleName}/Ext/Layoutdefs/{$module['name']}.php"
                    , function() use ($module, $relatedModuleName, $ext) {
                    $copyright = self::getCopyright();
                    $contents = <<<PHP
<?php
{$copyright}

PHP;

                    foreach ($ext['layout_defs'] as $layout_def_key => $layout_def) {
                        $str = self::varExport($layout_def);
                        $contents .= <<<PHP
\$layout_defs['{$relatedModuleName}']['subpanel_setup']['{$layout_def_key}'] = {$str};

PHP;
                    }
                    return $contents;
                });
            }
            //TODO: if ($updated) rebuild extensions
        }
    }
}
