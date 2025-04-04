<?php

return [

    'column_toggle' => [

        'heading' => 'Columns',

    ],

    'columns' => [

        'text' => [
            'account' => [
                'name' => 'Name',
                'legend' => 'Legend',
                'created_at' => 'Created at',
                'updated_at' => 'Updated at',
            ],
            'income' => [
                'name' => 'Name',
                'account' => 'Account',
                'budgets' => 'Amount',
                'month' => 'Month',
                'amount' => 'Amount',
                'created_at' => 'Created at',
                'updated_at' => 'Updated at',
                'is_fluctuating' => 'Fluctuating',
            ],
            'income_budget' => [
                'heading' => 'Incomes',
            ],
            'expense' => [
                'name' => 'Name',
                'category' => 'Category',
                'allocations' => 'Allocations',
                'realization' => 'Realization',
                'unrealized_amount' => 'Unrealized Amount',
                'usage_progress' => 'Usage Progress',
                'usage_percentage' => '% Percentage',
                'balance' => 'Balance',
                'tooltip' => 'Remaining balance from linked category accounts',
            ],
            'expense_allocations' => [
                'month' => 'Month',
                'amount' => 'Amount',
                'created_at' => 'Created at',
                'updated_at' => 'Updated at',
            ],
            'expense_realization' => [
                'description' => 'Description',
                'amount' => 'Amount',
                'realized_at' => 'Realized at',
                'completed' => 'Completed',
                'created_at' => 'Created at',
                'updated_at' => 'Updated at',
            ],

            'actions' => [
                'collapse_list' => 'Show :count less',
                'expand_list' => 'Show :count more',
            ],

            'more_list_items' => 'and :count more',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'bulk_select_group' => [
            'label' => 'Select/deselect group :title for bulk actions.',
        ],

        'search' => [
            'label' => 'Search',
            'placeholder' => 'Search',
            'indicator' => 'Search',
        ],

    ],

    'summary' => [

        'heading' => 'Summary',

        'subheadings' => [
            'all' => 'All :label',
            'group' => ':group summary',
            'page' => 'This page',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Average',
            ],

            'count' => [
                'label' => 'Count',
            ],

            'sum' => [
                'label' => 'Sum',
            ],

            'total' => [
                'label' => 'Total',
            ],

            'allocated' => [
                'label' => 'Allocated',
            ],

            'non_allocated' => [
                'label' => 'Non-allocated',
            ],
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Reorder records',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Group',
        ],

        'open_bulk_actions' => [
            'label' => 'Bulk actions',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [

        'heading' => 'No :model',

        'description' => 'Create a :model to get started.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Apply filters',
            ],

            'remove' => [
                'label' => 'Remove filter',
            ],

            'remove_all' => [
                'label' => 'Remove all filters',
                'tooltip' => 'Remove all filters',
            ],

            'reset' => [
                'label' => 'Reset',
            ],

        ],

        'heading' => 'Filters',

        'indicator' => 'Active filters',

        'multi_select' => [
            'placeholder' => 'All',
        ],

        'select' => [
            'placeholder' => 'All',
        ],

        'trashed' => [

            'label' => 'Deleted records',

            'only_trashed' => 'Only deleted records',

            'with_trashed' => 'With deleted records',

            'without_trashed' => 'Without deleted records',

        ],

    ],

    'grouping' => [
        'label' => [
            'category' => 'Category',
        ],

        'fields' => [

            'group' => [
                'label' => 'Group by',
                'placeholder' => 'Group by',
            ],

            'direction' => [

                'label' => 'Group direction',

                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => '1 record selected|:count records selected',

        'actions' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sort by',
            ],

            'direction' => [

                'label' => 'Sort direction',

                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],

            ],

        ],

    ],

];
