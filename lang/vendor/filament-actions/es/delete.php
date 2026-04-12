<?php

return [

    'single' => [

        'label' => 'Eliminar',

        'modal' => [

            'heading'     => 'Eliminar :label',
            'description' => '¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminado correctamente',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Eliminar seleccionados',

        'modal' => [

            'heading'     => 'Eliminar :label seleccionados',
            'description' => '¿Estás seguro de que deseas eliminar estos registros? Esta acción no se puede deshacer.',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminados correctamente',
            ],

            'deleted_partial' => [
                'title'                                 => 'Eliminados :count de :total',
                'missing_authorization_failure_message' => 'No tienes permiso para eliminar :count.',
                'missing_processing_failure_message'    => ':count no se pudieron eliminar.',
            ],

            'deleted_none' => [
                'title'                                 => 'No se pudo eliminar ninguno',
                'missing_authorization_failure_message' => 'No tienes permiso para eliminar :count.',
                'missing_processing_failure_message'    => ':count no se pudieron eliminar.',
            ],

        ],

    ],

];