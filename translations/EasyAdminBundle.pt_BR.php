<?php

return [
    'page_title' => [
        'dashboard' => 'Painel de Controle',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Editar %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Adicionar %entity_label_singular%',
        'exception' => 'Erro|Erros',
    ],

    'datagrid' => [
        'hidden_results' => 'Alguns resultados não podem ser exibidos porque você não tem permissão suficiente',
        'no_results' => 'Nenhum resultado encontrado.',
    ],

    'paginator' => [
        'first' => 'Primeira',
        'previous' => 'Anterior',
        'next' => 'Próxima',
        'last' => 'Última',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> de <strong>%results%</strong>',
        'results' => '{0} Sem resultados|{1} <strong>1</strong> resultado|]1,Inf] <strong>%count%</strong> resultados',
    ],

    'label' => [
        'true' => 'Sim',
        'false' => 'Não',
        'empty' => 'Vazio',
        'null' => 'Null',
        'object' => 'Objeto PHP',
        'inaccessible' => 'Inacessível',
        'inaccessible.explanation' => 'Não existe um método getter para esse campo ou a propriedade não é pública',
        'form.empty_value' => 'Nenhum',
    ],

    'field' => [
        'code_editor.view_code' => 'Visualizar código',
        'text_editor.view_content' => 'Visualizar conteúdo',
    ],

    'action' => [
        'entity_actions' => 'Ações',
        'new' => 'Adicionar %entity_label_singular%',
        'search' => 'Pesquisar',
        'detail' => 'Exibir',
        'edit' => 'Editar',
        'delete' => 'Excluir',
        'cancel' => 'Cancelar',
        'index' => 'Voltar para a lista',
        'deselect' => 'Remover seleção',
        'add_new_item' => 'Adicionar um novo item',
        'remove_item' => 'Remover o item',
        'choose_file' => 'Escolher arquivo',
        'close' => 'Fechar',
        'create' => 'Criar',
        'create_and_add_another' => 'Criar e adicionar outro',
        'create_and_continue' => 'Criar e continuar editando',
        'save' => 'Salvar alterações',
        'save_and_continue' => 'Salvar e continuar editando',
    ],

    'batch_action_modal' => [
        'title' => 'Você realmente deseja alterar os itens selecionados?',
        'content' => 'Esta operação é irreversível.',
        'action' => 'Continuar',
    ],

    'delete_modal' => [
        'title' => 'Você realmente deseja excluir esse item?',
        'content' => 'Não há como desfazer essa operação.',
    ],

    'filter' => [
        'title' => 'Filtros',
        'button.clear' => 'Limpar',
        'button.apply' => 'Aplicar',
        'label.is_equal_to' => 'é igual a',
        'label.is_not_equal_to' => 'é diferente de',
        'label.is_greater_than' => 'é maior que',
        'label.is_greater_than_or_equal_to' => 'é maior ou igual a',
        'label.is_less_than' => 'é menor que',
        'label.is_less_than_or_equal_to' => 'é menor ou igual a',
        'label.is_between' => 'entre',
        'label.contains' => 'contém',
        'label.not_contains' => 'não contém',
        'label.starts_with' => 'começa com',
        'label.ends_with' => 'termina com',
        'label.exactly' => 'é igual a',
        'label.not_exactly' => 'é diferente de',
        'label.is_same' => 'é igual a',
        'label.is_not_same' => 'é diferente de',
        'label.is_after' => 'depois de',
        'label.is_after_or_same' => 'depois ou igual a',
        'label.is_before' => 'antes de',
        'label.is_before_or_same' => 'antes ou igual a',
    ],

    'form' => [
        'are_you_sure' => 'Você não salvou as alterações feitas nesse formulário.',
        'tab.error_badge_title' => 'Uma entrada válida|%count% entradas inválidas',
        'slug.confirm_text' => 'Ao alterar o slug, você poderá quebrar links em outras páginas.',
    ],

    'user' => [
        'logged_in_as' => 'Logado como',
        'unnamed' => 'Usuário sem nome',
        'anonymous' => 'Usuário anônimo',
        'sign_out' => 'Sair',
        'exit_impersonation' => 'Sair da personificação',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Aparência',
            'light' => 'Clara',
            'dark' => 'Escura',
            'auto' => 'Automática',
        ],
        'locale' => 'Linguagem',
    ],

    'login_page' => [
        'username' => 'Usuário',
        'password' => 'Senha',
        'sign_in' => 'Entrar',
        'forgot_password' => 'Esqueceu sua senha?',
        'remember_me' => 'Manter conectado',
    ],

    'exception' => [
        'entity_not_found' => 'Este item não está mais disponível.',
        'entity_remove' => 'Esse item não pode ser excluído porque outros itens dependem dele.',
        'forbidden_action' => 'A ação requisitada não pode ser executada nesse item.',
        'insufficient_entity_permission' => 'Você não possui permissão para acessar este item.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Nenhum resultado encontrado',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Carregando mais resultados…',
    ],
];
