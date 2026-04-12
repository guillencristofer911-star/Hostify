<?php

return [

    'column_manager' => [

        'heading' => 'Columnas',

        'actions' => [

            'apply' => [
                'label' => 'Aplicar columnas',
            ],

            'reset' => [
                'label' => 'Restablecer columnas',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Acción|Acciones',
        ],

        'select' => [

            'loading_message' => 'Cargando...',

            'no_options_message' => 'No hay opciones disponibles.',

            'no_search_results_message' => 'No hay opciones que coincidan con su búsqueda.',

            'placeholder' => 'Seleccione una opción',

            'searching_message' => 'Buscando...',

            'search_prompt' => 'Empiece a escribir para buscar...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostrar :count menos',
                'expand_list' => 'Mostrar :count más',
            ],

            'more_list_items' => 'y :count más',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleccionar/deseleccionar todos los elementos para las acciones masivas.',
        ],

        'bulk_select_record' => [
            'label' => 'Seleccionar/deseleccionar el elemento :key para las acciones masivas.',
        ],

        'bulk_select_group' => [
            'label' => 'Seleccionar/deseleccionar grupo :title para acciones masivas.',
        ],

        'search' => [
            'label' => 'Búsqueda',
            'placeholder' => 'Buscar',
            'indicator' => 'Buscar',
        ],

    ],

    'summary' => [

        'heading' => 'Resumen',

        'subheadings' => [
            'all' => 'Todos :label',
            'group' => 'Resumen de :group',
            'page' => 'Esta página',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Media',
            ],

            'count' => [
                'label' => 'Recuento',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Terminar de reordenar registros',
        ],

        'enable_reordering' => [
            'label' => 'Reordenar registros',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Agrupar',
        ],

        'open_bulk_actions' => [
            'label' => 'Acciones masivas',
        ],

        'column_manager' => [
            'label' => 'Gestionar columnas',
        ],

    ],

    'empty' => [

        'heading' => 'No se encontraron registros',

        'description' => 'Cree un :model para comenzar.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplicar filtros',
            ],

            'remove' => [
                'label' => 'Quitar filtro',
            ],

            'remove_all' => [
                'label' => 'Quitar todos los filtros',
                'tooltip' => 'Quitar todos los filtros',
            ],

            'reset' => [
                'label' => 'Restablecer filtros',
            ],

        ],

        'heading' => 'Filtros',

        'indicator' => 'Filtros activos',

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [

            'placeholder' => 'Todos',

            'relationship' => [
                'empty_option_label' => 'Ninguno',
            ],

        ],

        'trashed' => [

            'label' => 'Registros eliminados',

            'only_trashed' => 'Solo eliminados',

            'with_trashed' => 'Incluir eliminados',

            'without_trashed' => 'Excluir eliminados',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Agrupar por',
            ],

            'direction' => [

                'label' => 'Dirección',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arrastre los registros para reordenarlos.',

    'selection_indicator' => [

        'selected_count' => '1 registro seleccionado|:count registros seleccionados',

        'actions' => [

            'select_all' => [
                'label' => 'Seleccionar todos (:count)',
            ],

            'deselect_all' => [
                'label' => 'Deseleccionar todos',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenar por',
            ],

            'direction' => [

                'label' => 'Dirección',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'default_model_label' => 'registro',

];