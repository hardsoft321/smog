<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class Searchdefs extends Metadata
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/searchdefs.php", function() use ($module) {
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
            //TODO: it generates LIKE query
            $fields[] = 'assigned_user_name';
            $fields[] = 'created_by_name';
            $columns = 3;
            $searchdefs = [
                'templateMeta' => [
                    'maxColumns' => $columns,
                    'maxColumnsBasic' => $columns,
                    'widths' => ['label' => '10', 'field' => '30'],
                ],
                'layout' => [
                    'basic_search' => [
                        'name',
                        [
                            'name' => 'current_user_only',
                            'label' => 'LBL_CURRENT_USER_FILTER',
                            'type' => 'bool',
                        ],
                    ],
                    'advanced_search' => $fields,
                ],
            ];
            $searchdefsString = self::varExport($searchdefs);
            return <<<PHP
<?php
{$copyright}
\$searchdefs['{$module['name']}'] = $searchdefsString;

PHP;
        });
    }
}
