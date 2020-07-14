<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class Detailviewdefs extends Metadata
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/detailviewdefs.php", function() use ($module) {
            $copyright = self::getCopyright();
            $fields = [];
            $fields[] = 'name';
            $dictionary = [];
            include "{$module['path']}/vardefs.php";
            if (!empty($dictionary[$module['object_name']])) {
                foreach ($dictionary[$module['object_name']]['fields'] as $field) {
                    if (empty($field['type'])) {
                        continue;
                    }
                    if (($field['type'] == 'link' || $field['type'] == 'id') && $field['name'] != 'currency_id') {
                        continue;
                    }
                    $detailField = $field['name'];
                    // if ($field['type'] == 'currency') {
                    //     $detailField = [
                    //         'name' => $field['name'],
                    //         'label' => '{$MOD.' . $field['vname'] . '} ({$CURRENCY})',
                    //     ];
                    // }
                    $fields[] = $detailField;
                }
            }
            $fields[] = 'description';
            $columns = 2;
            $viewdefs = [
                'DetailView' => [
                    'templateMeta' => [
                        'form' => [
                            'buttons' => ['EDIT', 'DUPLICATE', 'DELETE'],
                        ],
                        'maxColumns' => $columns,
                        'widths' => array_fill(0, $columns, ['label' => '10', 'field' => '30']),
                    ],
                    'panels' => [
                        'default' => self::groupToRows($fields, $columns),
                        'LBL_PANEL_ASSIGNMENT' => [
                            [ 'assigned_user_name',
                                '',
                            ],
                            [ [ 'name' => 'date_entered',
                                'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                                ],
                                [ 'name' => 'date_modified',
                                'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            $viewdefsString = self::varExport($viewdefs);
            return <<<PHP
<?php
{$copyright}
\$viewdefs['{$module['name']}'] = $viewdefsString;

PHP;
        });
    }
}
