<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class Editviewdefs extends Metadata
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/editviewdefs.php", function() use ($module) {
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
                    $fields[] = $field['name'];
                }
            }
            $fields[] = 'description';
            $columns = 2;
            $viewdefs = [
                'EditView' => [
                    'templateMeta' => [
                        'maxColumns' => $columns,
                        'widths' => array_fill(0, $columns, ['label' => '10', 'field' => '30']),
                    ],
                    'panels' => [
                        'default' => self::groupToRows($fields, $columns),
                        'LBL_PANEL_ASSIGNMENT' => self::groupToRows(['assigned_user_name'], $columns),
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
