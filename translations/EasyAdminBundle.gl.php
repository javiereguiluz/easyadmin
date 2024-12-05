<?php

return [
    'page_title' => [
        // 'dashboard' => '',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Modificar %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Crear %entity_label_singular%',
        'exception' => 'Error|Errores',
    ],

    'datagrid' => [
        // 'hidden_results' => '',
        'no_results' => 'Non se atoparon resultados.',
    ],

    'paginator' => [
        'first' => 'Primeira',
        'previous' => 'Anterior',
        'next' => 'Seguinte',
        'last' => 'Última',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> de <strong>%results%</strong>',
        // 'results' => '',
    ],

    'label' => [
        'true' => 'Si',
        'false' => 'Non',
        'empty' => 'Baleiro',
        'null' => 'Nulo',
        'object' => 'Obxecto PHP',
        'inaccessible' => 'Inaccesible',
        'inaccessible.explanation' => 'Este campo non ten ningún método getter ou a propiedade asociada non é publica',
        'form.empty_value' => 'Ningún',
    ],

    'field' => [
        // 'code_editor.view_code' => '',
        // 'text_editor.view_content' => '',
    ],

    'action' => [
        'entity_actions' => 'Accións',
        'new' => 'Crear %entity_label_singular%',
        'search' => 'Buscar',
        'detail' => 'Ver',
        'edit' => 'Modificar',
        'delete' => 'Borrar',
        'cancel' => 'Cancelar',
        'index' => 'Volver o listado',
        // 'deselect' => '',
        'add_new_item' => 'Engadir un elemento',
        'remove_item' => 'Eliminar este elemento',
        'choose_file' => 'Seleccionar arquivo',
        // 'close' => '',
        // 'create' => '',
        // 'create_and_add_another' => '',
        // 'create_and_continue' => '',
        // 'save' => '',
        // 'save_and_continue' => '',
    ],

    'batch_action_modal' => [
        // 'title' => '',
        // 'content' => '',
        // 'action' => '',
    ],

    'delete_modal' => [
        'title' => '¿Queres realmente borrar este elemento?',
        'content' => 'Esta acción non se pode desfacer.',
    ],

    'filter' => [
        // 'title' => '',
        // 'button.clear' => '',
        // 'button.apply' => '',
        // 'label.is_equal_to' => '',
        // 'label.is_not_equal_to' => '',
        // 'label.is_greater_than' => '',
        // 'label.is_greater_than_or_equal_to' => '',
        // 'label.is_less_than' => '',
        // 'label.is_less_than_or_equal_to' => '',
        // 'label.is_between' => '',
        // 'label.contains' => '',
        // 'label.not_contains' => '',
        // 'label.starts_with' => '',
        // 'label.ends_with' => '',
        // 'label.exactly' => '',
        // 'label.not_exactly' => '',
        // 'label.is_same' => '',
        // 'label.is_not_same' => '',
        // 'label.is_after' => '',
        // 'label.is_after_or_same' => '',
        // 'label.is_before' => '',
        // 'label.is_before_or_same' => '',
    ],

    'form' => [
        'are_you_sure' => 'Non se gardaron os cambios feitos neste formulario.',
        // 'tab.error_badge_title' => '',
    ],

    'user' => [
        'logged_in_as' => 'Conectado/a como',
        'unnamed' => 'Usuario sen nome',
        'anonymous' => 'Usuario anónimo',
        'sign_out' => 'Pechar sesión',
        // 'exit_impersonation' => '',
    ],

    'login_page' => [
        'username' => 'Nome de usuario',
        'password' => 'Contrasinal',
        'sign_in' => 'Iniciar sesión',
        // 'forgot_password' => '',
        // 'remember_me' => '',
    ],

    'exception' => [
        'entity_not_found' => 'Este elemento xa no está dispoñible.',
        'entity_remove' => 'Este elemento non se pode eliminar porque outros elementos dependen del.',
        'forbidden_action' => 'Non se pode realizar a acción solicitada neste elemento.',
        // 'insufficient_entity_permission' => 'You don't have permission to access this item.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Non se atoparon resultados',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Cargando máis resultados…',
    ],
];
