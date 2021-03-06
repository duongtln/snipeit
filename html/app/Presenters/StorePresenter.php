<?php

namespace App\Presenters;

/**
 * Class ComponentPresenter
 * @package App\Presenters
 */
class StorePresenter extends Presenter
{

    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                "field" => "id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.id'),
                "visible" => false
            ],
            [
                "field" => "company",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.company'),
                "visible" => true,
                "formatter" => 'companiesLinkObjFormatter',
            ],
            [
                "field" => "name",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.store'),
                "visible" => true,
                "formatter" => 'storesLinkFormatter',
            ], [
                "field" => "image",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.image'),
                "visible" => false,
                "formatter" => 'imageFormatter',
            ],
            [
                "field" => "department_count",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.departments'),

            ],
            [
                "field" => "contract_count",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.contracts'),

            ],
            [
                "field" => "location",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.location'),
                "formatter" => "locationsLinkObjFormatter"
            ],
        ];
        $layout[] = [
            "field" => "actions",
            "searchable" => false,
            "sortable" => false,
            "switchable" => false,
            "visible" => true,
            "title" => trans('table.actions'),
            "formatter" => "storesActionsFormatter",
        ];

        return json_encode($layout);
    }
}