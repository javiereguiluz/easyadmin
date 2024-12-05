<?php

return [
    'page_title' => [
        'dashboard' => 'لوحة التحكم',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular% تعديل',
        'index' => '%entity_label_plural%',
        'new' => '"%entity_label_singular%" جديد',
        'exception' => '{1} خطأ|{2} خطأن|]2,Inf] أخطاء ',
    ],

    'datagrid' => [
        'hidden_results' => 'لا يمكنك عرض بعض النتائج لأنك لا تملك أذونات كافية',
        'no_results' => 'لا توجد أيّ نتائج',
    ],

    'paginator' => [
        'first' => 'الأول',
        'previous' => 'السابق',
        'next' => 'التالي',
        'last' => 'الأخير',
        'counter' => '<strong>%results%</strong> / <strong>%end%</strong> - <strong>%start%</strong>',
        'results' => '{0} لا توجد أيّ نتائج |{1} <strong>1</strong> نتيجة|]1,Inf] <strong>%count%</strong> نتائج',
    ],

    'label' => [
        'true' => 'نعم',
        'false' => 'لا',
        'empty' => 'فارغ',
        'null' => 'لا شيء',
        'object' => 'كائن PHP',
        'inaccessible' => 'لا يمكن الوصول إليها',
        'inaccessible.explanation' => 'لا يوجد وصف الوصول لهذه الخاصية أو أنها ليست عامة.',
        'form.empty_value' => 'لا شيء',
    ],

    'field' => [
        'code_editor.view_code' => 'رؤية الكود',
        'text_editor.view_content' => 'رؤية المحتوى',
    ],

    'action' => [
        'entity_actions' => 'إجراءات',
        'new' => '%entity_label_singular% جديد',
        'search' => 'بحث',
        'detail' => 'إطلاع',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'cancel' => 'الغاء',
        'index' => 'رجوع إلى القائمة',
        'deselect' => 'الغاء تحديد',
        'add_new_item' => 'إضافة عنصر جديد',
        'remove_item' => 'حذف العنصر',
        'choose_file' => 'اختيار ملفّ',
        'close' => 'أغلاق',
        'create' => 'أنشاء',
        'create_and_add_another' => 'أنشاء و أضافة اخرى',
        'create_and_continue' => 'أنشاء و متابعة',
        'save' => 'حفظ',
        'save_and_continue' => 'حفظ و متابعة',
    ],

    'batch_action_modal' => [
        'title' => 'سوف تقوم بتطبيق الأجراء "%action_name%" على %num_items% عنصر',
        'content' => 'لا يمكنك التراجع عن هذا الإجراء.',
        'action' => 'استمرار',
    ],

    'delete_modal' => [
        'title' => 'هل تريد حذف هذا العنصر؟',
        'content' => 'هذا الإجراء غير قابل للإلغاء.',
    ],

    'filter' => [
        'title' => 'عوامل التصفية',
        'button.clear' => 'أعادة التعيين',
        'button.apply' => 'تطبيق',
        'label.is_equal_to' => 'يساوي',
        'label.is_not_equal_to' => 'لا يساوي ',
        'label.is_greater_than' => 'اكبر من',
        'label.is_greater_than_or_equal_to' => 'اكبر من او يساوي',
        'label.is_less_than' => 'أصغر من',
        'label.is_less_than_or_equal_to' => 'أصغر من أو يساوي',
        'label.is_between' => 'بين',
        'label.contains' => 'يحتوي',
        'label.not_contains' => 'لا يحتوي',
        'label.starts_with' => 'يبدء بـ',
        'label.ends_with' => 'ينتهي بـ',
        'label.exactly' => 'تماما كـ',
        'label.not_exactly' => 'ليس تماما كـ',
        'label.is_same' => 'مطابق',
        'label.is_not_same' => 'غير مطابق',
        'label.is_after' => 'بعد',
        'label.is_after_or_same' => 'بعد أو مطابق',
        'label.is_before' => 'قبل',
        'label.is_before_or_same' => 'قبل أو مطابق',
    ],

    'form' => [
        'are_you_sure' => 'لم يتّم حفظ التغييرات.',
        'tab.error_badge_title' => 'حقل واحد غير صالح|%count% حقول غير صالحة',
        'slug.confirm_text' => 'إذا قمت بتغيير الرابط الثابت ، يمكنك تعطيل الروابط الموجودة في الصفحات الأخرى.',
    ],

    'user' => [
        'logged_in_as' => 'تسجيل الدخول بإسم',
        'unnamed' => 'مستخدم بدون إسم',
        'anonymous' => 'مستخدم مجهول',
        'sign_out' => 'تسجيل الخروج',
        'exit_impersonation' => 'خروج وهمي',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'المظهر',
            'light' => 'الفاتح',
            'dark' => 'الداكن',
            'auto' => 'تلقائي',
        ],
        'locale' => 'لغة',
    ],

    'login_page' => [
        'username' => 'إسم المستخدم',
        'password' => 'كلمة السّر',
        'sign_in' => 'تسجيل الدخول',
        'forgot_password' => 'نسيت كلمة المرور الخاصة بك ؟ ',
        'remember_me' => 'تذكرنى',
    ],

    'exception' => [
        'entity_not_found' => 'هذا العنصر لم يعد متوفر',
        'entity_remove' => 'لا يمكنك حذف هذا العنصر لأن العناصر الأخرى تعتمد عليه.',
        'forbidden_action' => 'لا يمكنك تنفيذ الإجراء المطلوب على هذا العنصر.',
        'insufficient_entity_permission' => 'أنت لا تملك صلاحيات كافية للوصول الى هذا العنصر',
    ],

    'autocomplete' => [
        'no-results-found' => 'لم يتم العثور على أي نتائج',
        'no-more-results' => 'لا يوجد نتائج أٌخرى',
        'loading-more-results' => 'جاري تحميل نتائج إضافية…',
    ],
];
