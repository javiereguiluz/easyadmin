<?php

return [
    'page_title' => [
        'dashboard' => 'Վահանակ',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Ստեղծել նոր %entity_label_singular%',
        'exception' => 'Սխալ|Սխալներ|Սխալիկ',
    ],

    'datagrid' => [
        'hidden_results' => 'Որոշակի արդյունքները չեն կարող ցուցադրվել, քանի որ Դուք չունեք պահանջվող իրավասությունները',
        'no_results' => 'Ոչինչ չի գտնվել։',
    ],

    'paginator' => [
        'first' => 'Առաջին',
        'previous' => 'Նախորդ',
        'next' => 'Հաջորդ',
        'last' => 'Վերջին',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> ից <strong>%results%</strong>',
        'results' => '<strong>%count%</strong> արդյունք|<strong>%count%</strong> արդյունք|<strong>%count%</strong> արդյունքներ',
    ],

    'label' => [
        'true' => 'Այո',
        'false' => 'Ոչ',
        'empty' => 'Դատարկ',
        'null' => 'Null',
        'object' => 'PHP-օբյեկտ',
        'inaccessible' => 'Անհասանելի է',
        'inaccessible.explanation' => 'Այս դաշտի համար չկա կատարողը, կամ հատկանիշները հասանելի չեն',
        'form.empty_value' => 'Դատարկ է',
    ],

    'field' => [
        'code_editor.view_code' => 'Տեսնել կոդը',
        'text_editor.view_content' => 'Տեսնել պարունակությունը',
    ],

    'action' => [
        'entity_actions' => 'Գործողություն',
        'new' => 'Ստեղծել %entity_label_singular%',
        'search' => 'Որոնել',
        'detail' => 'Մանրամասն',
        'edit' => 'Խմբագրել',
        'delete' => 'Հեռացնել',
        'cancel' => 'Չեղարկել',
        'index' => 'Վերադառնալ ցուցակին',
        'deselect' => 'Հանել ընտրությունը',
        'add_new_item' => 'Ավելացնել նոր տարր',
        'remove_item' => 'Հեռացնել տարրը',
        'choose_file' => 'Ընտրել ֆայլը',
        'close' => 'Փակել',
        'create' => 'Ստեղծել',
        'create_and_add_another' => 'Ստեղծել և նորը ավելացնել',
        'create_and_continue' => 'Ստեղծել և շարունակել',
        'save' => 'Պահպանել',
        'save_and_continue' => 'Պահպանել և շարունակել',
    ],

    'batch_action_modal' => [
        'title' => 'Դուք պատրաստվում եք կատարել "%action_name%" գործողությունը %num_items% ընտրված տողերի համար',
        'content' => 'Այս գործողությունը չի կարող չեղարկվել.',
        'action' => 'Շարունակել',
    ],

    'delete_modal' => [
        'title' => 'Խնդրում ենք հաստատել, Դուք իրոք ցանկանում եք հեռացնել',
        'content' => 'Այս գործողությունը չի կարող չեղարկվել։',
    ],

    'filter' => [
        'title' => 'Ֆիլտրեր',
        'button.clear' => 'Մաքրել',
        'button.apply' => 'Կիրառել',
        'label.is_equal_to' => 'հավասար է',
        'label.is_not_equal_to' => 'հավասար չէ',
        'label.is_greater_than' => 'մեծ է',
        'label.is_greater_than_or_equal_to' => 'մեծ է կամ հավասար է',
        'label.is_less_than' => 'փոքր է',
        'label.is_less_than_or_equal_to' => 'փոքր է կամ հավասար է',
        'label.is_between' => 'միջակայքում է',
        'label.contains' => 'պարունակում է',
        'label.not_contains' => 'չի պարունակում',
        'label.starts_with' => 'սկսվում է',
        'label.ends_with' => 'ավարտվում է',
        'label.exactly' => 'ստույգ տեքստով',
        'label.not_exactly' => 'ոչ ստույգ',
        'label.is_same' => 'նույնն է',
        'label.is_not_same' => 'տարբեր է',
        'label.is_after' => 'հետո',
        'label.is_after_or_same' => 'հետո կամ նույնը է',
        'label.is_before' => 'առաջ',
        'label.is_before_or_same' => 'առաջ կամ նույնը է',
    ],

    'form' => [
        'are_you_sure' => 'Դուք չեք հիշել կատարված փոփոխությունները',
        'tab.error_badge_title' => 'Մեկ անհաջող փորձ|%count% անհաջող փորձեր|%count% անհաջող փորձերից',
        'slug.confirm_text' => 'Եթե փոխեք տեքստի իդենտիֆիկատորը, կարող եք խափանել այլ էջերում գտնվող հղումները.',
    ],

    'user' => [
        'logged_in_as' => 'Դուք մուտք եք գործել որպես',
        'unnamed' => 'Անանուն օգտվող',
        'anonymous' => 'Անանուն օգտվող',
        'sign_out' => 'Ելք',
        'exit_impersonation' => 'Դուրս գալ',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Արտաքին ձևավորում',
            'light' => 'Լուսավոր',
            'dark' => 'Մութ',
            'auto' => 'Ավտոմատ',
        ],
        'locale' => 'Լեզու',
    ],

    'login_page' => [
        'username' => 'Օգտանուն',
        'password' => 'Գաղտնաբառ',
        'sign_in' => 'Մուտք',
        'forgot_password' => 'Մոռացե՞լ եք գաղտնաբառը',
        'remember_me' => 'Հիշիր ինձ',
    ],

    'exception' => [
        'entity_not_found' => 'Տարրն այլևս հասանելի չէ։',
        'entity_remove' => 'Տարրը չի կարող հեռացվել, քանի որ մեկ այլ տարր կախված է դրանից։',
        'forbidden_action' => 'Պահանջվող գործողությունը չի թույլատրվում։',
        'insufficient_entity_permission' => 'Դուք չունեք բավարար թույլտվություններ։',
    ],

    'autocomplete' => [
        'no-results-found' => 'Համընկնումներ չեն գտնվել',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Տվյալների բեռնում…',
    ],
];
