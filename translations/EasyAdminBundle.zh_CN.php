<?php

return [
    'page_title' => [
        'dashboard' => '控制台',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '编辑 %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => '新增 %entity_label_singular%',
        'exception' => '错误|错误',
    ],

    'datagrid' => [
        'hidden_results' => '因为你没有足够的权限，隐藏了部分结果。',
        'no_results' => '没有找到结果.',
    ],

    'paginator' => [
        'first' => '首页',
        'previous' => '上一页',
        'next' => '下一页',
        'last' => '尾页',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> of <strong>%results%</strong>',
        'results' => '{0} 无结果|{1} <strong>1</strong> 条结果|]1,Inf] <strong>%count%</strong> 条结果',
    ],

    'label' => [
        'true' => '可用',
        'false' => '不可用',
        'empty' => '空',
        'null' => '未赋值',
        'object' => 'PHP对象',
        'inaccessible' => '无法获取',
        'inaccessible.explanation' => '该字段的Getter方法缺失或该字段不是公共属性',
        'form.empty_value' => '空',
    ],

    'field' => [
        'code_editor.view_code' => '查看代码',
        'text_editor.view_content' => '查看内容',
    ],

    'action' => [
        'entity_actions' => '操作',
        'new' => '新增 %entity_label_singular%',
        'search' => '搜索',
        'detail' => '展示',
        'edit' => '编辑',
        'delete' => '删除',
        'cancel' => '取消',
        'index' => '返回列表',
        'deselect' => '反选',
        'add_new_item' => '添加一项',
        'remove_item' => '删除一项',
        'choose_file' => '选择文件',
        'close' => '关闭',
        'create' => '添加',
        'create_and_add_another' => '添加并创建另一个',
        'create_and_continue' => '添加并继续',
        'save' => '保存',
        'save_and_continue' => '保存并继续',
    ],

    'batch_action_modal' => [
        'title' => '标题',
        'content' => '内容',
        'action' => '操作',
    ],

    'delete_modal' => [
        'title' => '是否删除',
        'content' => '是否删除，该操作不可恢复',
    ],

    'filter' => [
        'title' => '过滤器',
        'button.clear' => '清除',
        'button.apply' => '应用',
        'label.is_equal_to' => '相同于',
        'label.is_not_equal_to' => '不同于',
        'label.is_greater_than' => '大于',
        'label.is_greater_than_or_equal_to' => '大于或等于',
        'label.is_less_than' => '小于',
        'label.is_less_than_or_equal_to' => '小于或等于',
        'label.is_between' => '之间',
        'label.contains' => '包含',
        'label.not_contains' => '不包含',
        'label.starts_with' => '起始于',
        'label.ends_with' => '结束于',
        'label.exactly' => '精确',
        'label.not_exactly' => '不精确',
        'label.is_same' => '相同',
        'label.is_not_same' => '不相同',
        'label.is_after' => '之后',
        'label.is_after_or_same' => '相同或之后',
        'label.is_before' => '之前',
        'label.is_before_or_same' => '之前或相同',
    ],

    'form' => [
        'are_you_sure' => '该表单的更改还未保存',
        'tab.error_badge_title' => '有 1 条输入错误|有 %count% 条输入错误',
        'slug.confirm_text' => '如果你修改了url别名，其他页面的引用链接会出现错误。',
    ],

    'user' => [
        'logged_in_as' => '当前登录用户',
        'unnamed' => '未命名用户',
        'anonymous' => '匿名用户',
        'sign_out' => '退出',
        'exit_impersonation' => '退出模拟用户',
    ],

    'settings' => [
        'appearance' => [
            'label' => '外观',
            'light' => '浅色',
            'dark' => '深色',
            'auto' => '自动',
        ],
        'locale' => '语',
    ],

    'login_page' => [
        'username' => '用户名',
        'password' => '密码',
        'sign_in' => '登录',
        'forgot_password' => '忘记密码?',
        'remember_me' => '记住我',
    ],

    'exception' => [
        'entity_not_found' => '没有找到当前记录',
        'entity_remove' => '该条记录不可删除，因为有其他记录依赖该条记录。',
        'forbidden_action' => '无权执行该操作',
        'insufficient_entity_permission' => '你没有权限访问该条记录',
    ],

    'autocomplete' => [
        'no-results-found' => '未找到结果',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => '载入更多结果…',
    ],
];
