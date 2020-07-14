<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module\metadata;

use smog\module\Metadata;

class Subpanels extends Metadata
{
    public static function createDefault($options)
    {
        $module = self::getModule($options);
        self::writeFileIfNotExists("{$module['path']}/metadata/subpanels/default.php", function() use ($module) {
            $copyright = self::getCopyright();
            $fields = [];
            $fields['name'] = [
                'vname' => 'LBL_NAME',
                'widget_class' => 'SubPanelDetailViewLink',
                'width' => '20%',
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
                    $subpanelField = [
                        'vname' => $field['vname'],
                        'width' => '10%',
                    ];
                    if ($field['type'] == 'relate') {
                        $subpanelField['widget_class'] = 'SubPanelDetailViewLink';
                        $subpanelField['target_record_key'] = $field['id_name'];
                        $subpanelField['target_module'] = $field['module'];
                    }
                    elseif ($field['type'] == 'currency') {
                        $fields['currency_id'] = [
                            'usage' => 'query_only',
                        ];
                    }
                    $fields[$field['name']] = $subpanelField;
                }
            }
            $fields['assigned_user_name'] = [
                'vname' => 'LBL_LIST_ASSIGNED_USER',
                'widget_class' => 'SubPanelDetailViewLink',
                'target_record_key' => 'assigned_user_id',
                'target_module' => 'Employees',
                'width' => '10%',
            ];
            $fields['date_entered'] = [
                'vname' => 'LBL_DATE_ENTERED',
                'width' => '10%',
            ];
            $fields['edit_button'] = [
                'vname' => 'LBL_EDIT_BUTTON',
                'widget_class' => 'SubPanelEditButton',
                'width' => '4%',
            ];
            $fields['remove_button'] = [
                'vname' => 'LBL_REMOVE',
                'widget_class' => 'SubPanelRemoveButton',
                'width' => '4%',
            ];

            $subpanelLayout = [
                'top_buttons' => [
                    ['widget_class' => 'SubPanelTopCreateButton'],
                    ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => $module['name']],
                ],
                'where' => '',
                'list_fields' => $fields,
            ];
            $subpanelLayoutString = self::varExport($subpanelLayout);
            return <<<PHP
<?php
{$copyright}
\$subpanel_layout = $subpanelLayoutString;

PHP;
        });
    }
}
