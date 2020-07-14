<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class Listviewdefs extends Metadata
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/listviewdefs.php", function() use ($module) {
            $copyright = self::getCopyright();
            $fields = [];
            $fields['NAME'] = [
                'label' => 'LBL_NAME',
                'width' => 20,
                'link' => true,
                'default' => true,
            ];
            $dictionary = [];
            include "{$module['path']}/vardefs.php";
            if (!empty($dictionary[$module['object_name']])) {
                foreach ($dictionary[$module['object_name']]['fields'] as $field) {
                    if (empty($field['type'])) {
                        continue;
                    }
                    if ($field['type'] == 'link' || $field['type'] == 'id') {
                        continue;
                    }
                    $fields[\strtoupper($field['name'])] = [
                        'label' => $field['vname'],
                        'width' => 10,
                        'default' => false,
                    ];
                }
            }
            $fields['ASSIGNED_USER_NAME'] = [
                'label' => 'LBL_LIST_ASSIGNED_USER',
                'width' => 10,
                'module' => 'Employees',
                'id' => 'ASSIGNED_USER_ID',
                'default' => true,
            ];
            $fields['DATE_ENTERED'] = [
                'label' => 'LBL_DATE_ENTERED',
                'width' => 10,
                'default' => true,
            ];
            $fields['CREATED_BY_NAME'] = [
                'label' => 'LBL_CREATED',
                'width' => 10,
                'default' => false,
            ];
            $fields['DATE_MODIFIED'] = [
                'label' => 'LBL_DATE_MODIFIED',
                'width' => 10,
                'default' => false,
            ];
            $fields['MODIFIED_BY_NAME'] = [
                'label' => 'LBL_MODIFIED',
                'width' => 10,
                'default' => false,
            ];
            $fieldsString = self::varExport($fields);
            return <<<PHP
<?php
{$copyright}
\$listViewDefs['{$module['name']}'] = $fieldsString;

PHP;
        });
    }
}
