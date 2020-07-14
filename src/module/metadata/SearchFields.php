<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class SearchFields extends Metadata
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/SearchFields.php", function() use ($module) {
            $copyright = self::getCopyright();
            $searchFields = [];
            $searchFields['current_user_only'] = [
                'query_type' => 'default',
                'db_field' => ['assigned_user_id'],
                'my_items' => true,
            ];
            $dictionary = [];
            include "{$module['path']}/vardefs.php";
            if (!empty($dictionary[$module['object_name']])) {
                foreach ($dictionary[$module['object_name']]['fields'] as $field) {
                    if (!empty($field['enable_range_search'])) {
                        $searchFields['range_' . $field['name']] = ['query_type' => 'default', 'enable_range_search' => true];
                        $searchFields['start_range_' . $field['name']] = ['query_type' => 'default', 'enable_range_search' => true];
                        $searchFields['end_range_' . $field['name']] = ['query_type' => 'default', 'enable_range_search' => true];
                        if ($field['type'] === 'date' || $field['type'] === 'datetime') {
                            $searchFields['range_' . $field['name']]['is_date_field'] = true;
                            $searchFields['start_range_' . $field['name']]['is_date_field'] = true;
                            $searchFields['end_range_' . $field['name']]['is_date_field'] = true;
                        }
                    }
                }
            }
            $searchFields['range_date_entered'] = ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true];
            $searchFields['start_range_date_entered'] = ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true];
            $searchFields['end_range_date_entered'] = ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true];
            $searchFieldsString = self::varExport($searchFields);
            return <<<PHP
<?php
{$copyright}
\$searchFields['{$module['name']}'] = $searchFieldsString;

PHP;
        });
    }
}
