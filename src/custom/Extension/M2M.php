<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\custom\Extension;

use smog\module\Bean;
use smog\ArgumentException;

class M2M extends Bean
{
    public static function create($options)
    {
        if (empty($options['left'])) {
            throw new ArgumentException('Undefined left hand side module name. Use --left option.');
        }
        if (empty($options['right'])) {
            throw new ArgumentException('Undefined right hand side module name. Use --right option.');
        }
        //TODO: check modules exist
        $leftModule = self::getModule(['module' => $options['left']]);
        $rightModule = self::getModule(['module' => $options['right']]);
        $relName = "{$leftModule['name']}_{$rightModule['name']}";

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/metadata/{$leftModule['name']}_{$rightModule['name']}.php"
            , function() use ($leftModule, $rightModule, $relName) {
            $tableName = "{$leftModule['table_name']}_{$rightModule['table_name']}";
            $lhsKey = \strtolower($leftModule['object_name']) . '_id';
            $rhsKey = \strtolower($rightModule['object_name']) . '_id';
            $metadata = [
                'table' => $tableName,
                'true_relationship_type' => 'many-to-many',
                'relationships' => [
                    $relName => [
                        'lhs_module' => $leftModule['name'],
                        'lhs_table' => $leftModule['table_name'],
                        'lhs_key' => 'id',
                        'rhs_module' => $rightModule['name'],
                        'rhs_table' => $rightModule['table_name'],
                        'rhs_key' => 'id',
                        'relationship_type' => 'many-to-many',
                        'join_table' => $tableName,
                        'join_key_lhs' => $lhsKey,
                        'join_key_rhs' => $rhsKey,
                    ],
                ],
                'fields' => [
                    [ 'name' => 'id', 'type' => 'id' ],
                    [ 'name' => $lhsKey, 'type' => 'id' ],
                    [ 'name' => $rhsKey, 'type' => 'id' ],
                    [ 'name' => 'date_modified', 'type' => 'datetime'],
                    [ 'name' => 'deleted', 'type' => 'bool', 'default' => '0', 'required' => true ],
                ],
                'indices' => [
                    [
                        'name' => $tableName . '_pk',
                        'type' => 'primary',
                        'fields' => [ 'id' ],
                    ],
                    [
                        'name' => "{$tableName}_{$lhsKey}_idx",
                        'type' => 'alternate_key',
                        'fields' => [
                            $lhsKey,
                            $rhsKey,
                        ],
                    ],
                    [
                        'name' => "{$tableName}_{$rhsKey}_idx",
                        'type' => 'alternate_key',
                        'fields' => [
                            $rhsKey,
                            $lhsKey,
                        ],
                    ],
                ],
            ];
            $str = self::varExport($metadata);
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
\$dictionary['{$tableName}'] = {$str};

PHP;
        });

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/Extension/application/Ext/TableDictionary/{$leftModule['name']}_{$rightModule['name']}.php"
            , function() use ($leftModule, $rightModule) {
            return <<<PHP
<?php
include('custom/metadata/{$leftModule['name']}_{$rightModule['name']}.php');

PHP;
        });

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/Extension/modules/{$leftModule['name']}/Ext/Vardefs/{$rightModule['name']}.php"
            , function() use ($leftModule, $rightModule, $relName) {
            $fieldDef = [
                'name' => $rightModule['name'],
                'vname' => 'LBL_' . \strtoupper($rightModule['name']),
                'type' => 'link',
                'relationship' => $relName,
                'module' => $rightModule['name'],
                'link_type' => 'many',
                'source' => 'non-db',
            ];
            $str = self::varExport($fieldDef);
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
\$dictionary['{$leftModule['object_name']}']['fields']['{$rightModule['name']}'] = {$str};

PHP;
        });

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/Extension/modules/{$rightModule['name']}/Ext/Vardefs/{$leftModule['name']}.php"
            , function() use ($leftModule, $rightModule, $relName) {
            $fieldDef = [
                'name' => $leftModule['name'],
                'vname' => 'LBL_' . \strtoupper($leftModule['name']),
                'type' => 'link',
                'relationship' => $relName,
                'module' => $leftModule['name'],
                'link_type' => 'many',
                'source' => 'non-db',
            ];
            $str = self::varExport($fieldDef);
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
\$dictionary['{$rightModule['object_name']}']['fields']['{$leftModule['name']}'] = {$str};

PHP;
        });

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/Extension/modules/{$leftModule['name']}/Ext/Layoutdefs/{$rightModule['name']}.php"
            , function() use ($leftModule, $rightModule) {
            $layoutDef = [
                'order' => rand(1, 100),
                'sort_by' => 'date_entered',
                'sort_order' => 'asc',
                'module' => $rightModule['name'],
                'refresh_page' => 0,
                'subpanel_name' => 'default',
                'title_key' => 'LBL_' . \strtoupper($rightModule['name']),
                'get_subpanel_data' => $rightModule['name'],
                'top_buttons' => [
                    [ 'widget_class' => 'SubPanelTopButtonQuickCreate' ],
                    [ 'widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect' ],
                ],
            ];
            $layout_def_key = \strtolower($rightModule['name']);
            $str = self::varExport($layoutDef);
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
\$layout_defs['{$leftModule['name']}']['subpanel_setup']['{$layout_def_key}'] = {$str};

PHP;
        });

        self::writeFileIfNotExists(
            "{$leftModule['root']}/custom/Extension/modules/{$rightModule['name']}/Ext/Layoutdefs/{$leftModule['name']}.php"
            , function() use ($leftModule, $rightModule) {
            $layoutDef = [
                'order' => rand(1, 100),
                'sort_by' => 'date_entered',
                'sort_order' => 'asc',
                'module' => $leftModule['name'],
                'refresh_page' => 0,
                'subpanel_name' => 'default',
                'title_key' => 'LBL_' . \strtoupper($leftModule['name']),
                'get_subpanel_data' => $leftModule['name'],
                'top_buttons' => [
                    [ 'widget_class' => 'SubPanelTopButtonQuickCreate' ],
                    [ 'widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect' ],
                ],
            ];
            $layout_def_key = \strtolower($leftModule['name']);
            $str = self::varExport($layoutDef);
            $copyright = self::getCopyright();
            return <<<PHP
<?php
{$copyright}
\$layout_defs['{$rightModule['name']}']['subpanel_setup']['{$layout_def_key}'] = {$str};

PHP;
        });
        //TODO: rebuild extensions
    }
}
