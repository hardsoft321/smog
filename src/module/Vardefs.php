<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

use smog\ArgumentException;

class Vardefs extends Bean
{
    public static function create($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/vardefs.php", function() use ($module, $options) {
            $fields = [];
            $indices = [];
            $relationships = [];
            if (!empty($options['fields'])) {
                $m = preg_match_all ('/(?P<name>\w+):(?P<type>\w+)(\[(?P<params>[^\]]*)\])?/', $options['fields'], $matches);
                if ($m === false) {
                    throw new ArgumentException('Can not parse `fields` option.');
                }
                foreach ($matches[0] as $i => $str) {
                    $name = $matches['name'][$i];
                    $type = $matches['type'][$i];
                    $params = explode(',', $matches['params'][$i]);
                    $params = array_map('trim', $params);
                    $params = array_filter($params);
                    $field = [
                        'name' => $name,
                        'vname' => 'LBL_' . \mb_strtoupper($name),
                        'audited' => true,
                        'type' => $type,
                    ];
                    if (array_search('required', $params) !== false) {
                        $field['required'] = true;
                    }
                    if ($type == 'varchar' || $type == 'enum' || $type == 'multienum') {
                        if (!empty($params[0]) && \is_numeric($params[0])) {
                            $field['len'] = $params[0];
                        }
                    }
                    else if ($type == 'id') {
                        if (!empty($params)) {
                            $relatedModuleName = $params[0];
                            $relatedModuleObjectName = self::getObjectNameForModuleName($relatedModuleName);
                            $relatedModule = [
                                'name' => $relatedModuleName,
                                'root' => $module['root'],
                                'object_name' => $relatedModuleObjectName,
                            ];
                            $relatedTableName = self::getTableNameForModule($relatedModule);
                            $relName = strtolower($relatedModuleObjectName ?? $relatedModuleName)
                                . '_' . strtolower($module['name']);
                            $rel = [
                                'lhs_module'=> $relatedModuleName,
                                'lhs_table'=> $relatedTableName,
                                'lhs_key' => 'id',
                                'rhs_module'=> $module['name'],
                                'rhs_table'=> $module['table_name'],
                                'rhs_key' => $name,
                                'relationship_type'=>'one-to-many',
                            ];
                            $linkName = \preg_replace('/_id$/', '_link', $name); //TODO: без _link, но не равно $name
                            $linkField = [
                                'name' => $linkName,
                                'vname' => 'LBL_' . \mb_strtoupper($linkName),
                                'type' => 'link',
                                'relationship' => $relName,
                                'module' => $relatedModuleName,
                                'link_type' => 'one',
                                'source' => 'non-db',
                            ];
                            $relateName = \preg_replace('/_id$/', '_name', $name);
                            $relateField = [
                                'name' => $relateName,
                                'vname' => 'LBL_' . \mb_strtoupper($relateName),
                                'id_name' => $name,
                                'type' => 'relate',
                                'link' => $linkName,
                                'module' => $relatedModuleName,
                                'table' => $relatedTableName,
                                'rname' => 'name',
                                'source' => 'non-db',
                            ];
                            if (isset($field['required'])) {
                                $relateField['required'] = $field['required'];
                                unset($field['required']);
                            }
                            $indexName = 'idx_' . \strtolower($module['object_name']) . '_' . $name;
                            $index = [
                                'name' => $indexName,
                                'type' => 'index',
                                'fields' => [$name],
                            ];

                            $fields[$linkName] = $linkField;
                            $fields[$relateName] = $relateField;
                            $indices[] = $index;
                            $relationships[$relName] = $rel;
                        }
                    }
                    else if ($type == 'date') {
                        $field['enable_range_search'] = true;
                        $field['options'] = 'date_range_search_dom';
                    }
                    else if ($type == 'currency') {
                        $fields['currency_id'] = [
                            'name' => 'currency_id',
                            'vname' => 'LBL_CURRENCY',
                            'type' => 'id',
                            'group' => 'currency_id',
                            'function' => ['name' => 'getCurrencyDropDown', 'returns' => 'html'],
                        ];
                        $field['dbType'] = 'double';
                        $field['options'] = 'numeric_range_search_dom';
                        $field['enable_range_search'] = true;
                        // TODO: currency_name audited
                    }
                    $fields[$field['name']] = $field;
                }
            }
            $implements = [];
            if (!empty($options['implements'])) {
                $implements = explode(',', $options['implements']);
            }
            $copyright = self::getCopyright();
            $dictionary = [
                'table' => $module['table_name'],
                'audited' => true,
                'fields' => $fields,
                'indices' => $indices,
                'relationships' => $relationships,
            ];
            $dictionaryString = self::varExport($dictionary);
            $implementsString = !empty($implements) //TODO: validate implements
                ? "
VardefManager::createVardef('{$module['name']}', '{$module['object_name']}', " . self::varExport($implements) . ");
"
                : '';
            $contents = <<<PHP
<?php
{$copyright}
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

\$dictionary['{$module['object_name']}'] = $dictionaryString;

PHP;
            if (!empty($implementsString)) {
                $contents .= $implementsString;
            }
            $contents .= <<<PHP

\$dictionary['{$module['object_name']}']['fields']['name']['audited'] = true;

PHP;
            return $contents;
        });
    }
}
