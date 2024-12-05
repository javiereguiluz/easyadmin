<?php

return [
    'page_title' => [
        'dashboard' => '儀表板',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '編輯%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => '建立%entity_label_singular%',
        'exception' => '錯誤|錯誤',
    ],

    'datagrid' => [
        'hidden_results' => '由於權限不足，部分結果無法顯示',
        'no_results' => '未找到結果。',
    ],

    'paginator' => [
        'first' => '第一頁',
        'previous' => '上一頁',
        'next' => '下一頁',
        'last' => '最後一頁',
        'counter' => '<strong>%start%</strong> – <strong>%end%</strong> 共 <strong>%results%</strong> 個',
        'results' => '{0} 無結果|{1} <strong>1</strong> 個結果|]1,Inf] <strong>%count%</strong> 個結果',
    ],

    'label' => [
        'true' => '是',
        'false' => '否',
        'empty' => '空',
        'null' => 'Null',
        'object' => 'PHP 物件',
        'inaccessible' => '無法存取',
        'inaccessible.explanation' => '此欄位的 Getter 方法不存在或欄位不是 public',
        'form.empty_value' => '無',
    ],

    'field' => [
        'code_editor.view_code' => '檢視程式碼',
        'text_editor.view_content' => '檢視內容',
    ],

    'action' => [
        'entity_actions' => '動作',
        'new' => '新增%entity_label_singular%',
        'search' => '搜尋',
        'detail' => '顯示',
        'edit' => '編輯',
        'delete' => '刪除',
        'cancel' => '取消',
        'index' => '返回列表',
        'deselect' => '取消選擇',
        'add_new_item' => '新增項目',
        'remove_item' => '移除項目',
        'choose_file' => '選擇檔案',
        'close' => '關閉',
        'create' => '建立',
        'create_and_add_another' => '建立並繼續新增',
        'create_and_continue' => '建立並繼續編輯',
        'save' => '儲存更改',
        'save_and_continue' => '儲存並繼續編輯',
    ],

    'batch_action_modal' => [
        'title' => '您即將對 %num_items% 個項目執行「%action_name%」動作。',
        'content' => '此動作無法復原。',
        'action' => '繼續',
    ],

    'delete_modal' => [
        'title' => '您確定要刪除此項目嗎？',
        'content' => '此動作無法復原。',
    ],

    'filter' => [
        'title' => '篩選器',
        'button.clear' => '清除',
        'button.apply' => '套用',
        'label.is_equal_to' => '等於',
        'label.is_not_equal_to' => '不等於',
        'label.is_greater_than' => '大於',
        'label.is_greater_than_or_equal_to' => '大於或等於',
        'label.is_less_than' => '小於',
        'label.is_less_than_or_equal_to' => '小於或等於',
        'label.is_between' => '處於範圍',
        'label.contains' => '包含',
        'label.not_contains' => '不包含',
        'label.starts_with' => '開始於',
        'label.ends_with' => '結尾於',
        'label.exactly' => '完全等於',
        'label.not_exactly' => '不完全等於',
        'label.is_same' => '相同',
        'label.is_not_same' => '不相同',
        'label.is_after' => '之後於',
        'label.is_after_or_same' => '之後或相同於',
        'label.is_before' => '之前於',
        'label.is_before_or_same' => '之前或相同於',
    ],

    'form' => [
        'are_you_sure' => '您尚未儲存此表單上的更動。',
        'tab.error_badge_title' => '1 個無效輸入|%count% 個無效輸入',
        'slug.confirm_text' => '如果您更改了 slug，其他頁面的連結可能會失效。',
    ],

    'user' => [
        'logged_in_as' => '登入身分',
        'unnamed' => '未命名使用者',
        'anonymous' => '匿名使用者',
        'sign_out' => '登出',
        'exit_impersonation' => '離開使用者模擬',
    ],

    'settings' => [
        'appearance' => [
            'label' => '外觀',
            'light' => '亮色',
            'dark' => '暗色',
            'auto' => '自動',
        ],
        'locale' => '語言',
    ],

    'login_page' => [
        'username' => '使用者名稱',
        'password' => '密碼',
        'sign_in' => '登入',
        'forgot_password' => '忘記密碼？',
        'remember_me' => '記住我',
    ],

    'exception' => [
        'entity_not_found' => '此項目已不能取用。',
        'entity_remove' => '由於其他項目依賴於此項目，因此無法刪除。',
        'forbidden_action' => '無法對此項目執行請求的動作。',
        'insufficient_entity_permission' => '您無權取用此項目。',
    ],

    'autocomplete' => [
        'no-results-found' => '未找到結果',
        'no-more-results' => '沒有更多結果',
        'loading-more-results' => '正在載入更多結果⋯⋯',
    ],
];
