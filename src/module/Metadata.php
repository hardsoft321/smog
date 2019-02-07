<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

namespace smog\module;

class Metadata extends Bean
{
    public static function createAll($options)
    {
        metadata\Detailviewdefs::create($options);
        metadata\Editviewdefs::create($options);
        metadata\Listviewdefs::create($options);
        metadata\Quickcreatedefs::create($options);
        metadata\Searchdefs::create($options);
        metadata\SearchFields::create($options);
        metadata\Studio::create($options);
        metadata\Subpaneldefs::create($options);
        metadata\Subpanels::createDefault($options);
    }

    public static function groupToRows(array $fields, int $columns)
    {
        $rows = [];
        $row = [];
        foreach ($fields as $name) {
            if (count($row) == $columns) {
                $rows[] = $row;
                $row = [];
            }
            $row[] = $name;
        }
        for ($i = count($row); $i < $columns; $i++) {
            $row[] = '';
        }
        $rows[] = $row;
        return $rows;
    }
}
