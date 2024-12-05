<?php

return [
    'page_title' => [
        'dashboard' => 'داشبورد',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'ویرایش %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'ایجاد %entity_label_singular%',
        'exception' => 'خطا|خطاها',
    ],

    'datagrid' => [
        'hidden_results' => 'برخی از نتایج به دلیل نداشتن مجوز کافی نمایش داده نمی‌شوند',
        'no_results' => 'نتیجه‌ای یافت نشد',
    ],

    'paginator' => [
        'first' => 'اولین',
        'previous' => 'قبلی',
        'next' => 'بعدی',
        'last' => 'آخرین',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> از <strong>%results%</strong>',
        'results' => '{0} نتیجه‌ای یافت نشد|{1} <strong>1</strong> نتیجه|]1,Inf] <strong>%count%</strong> نتیجه',
    ],

    'label' => [
        'true' => 'بله',
        'false' => 'خیر',
        'empty' => 'خالی',
        'null' => 'تهی',
        'object' => 'شی گرایی در PHP',
        'inaccessible' => 'غیرقابل دسترس',
        'inaccessible.explanation' => 'متد دریافت کننده برای این فیلد وجود ندارد یا فیلد عمومی نیست',
        'form.empty_value' => 'هیچ',
    ],

    'field' => [
        'code_editor.view_code' => 'مشاهده کد',
        'text_editor.view_content' => 'مشاهده محتوا',
    ],

    'action' => [
        'entity_actions' => 'اقدامات',
        'new' => 'افزودن %entity_label_singular%',
        'search' => 'جستجو',
        'detail' => 'نمایش',
        'edit' => 'ویرایش',
        'delete' => 'حذف',
        'cancel' => 'انصراف',
        'index' => 'بازگشت به فهرست',
        'deselect' => 'لغو انتخاب',
        'add_new_item' => 'افزودن آیتم جدید',
        'remove_item' => 'حذف آیتم',
        'choose_file' => 'انتخاب فایل',
        'close' => 'بستن',
        'create' => 'ایجاد کردن',
        'create_and_add_another' => 'ایجاد و افزودن یکی دیگر',
        'create_and_continue' => 'ایجاد و ادامه ویرایش',
        'save' => 'ذخیره تغییرات',
        'save_and_continue' => 'ذخیره کردن و ادامه ویرایش',
    ],

    'batch_action_modal' => [
        'title' => 'شما در حال اعمال "%action_name%" روی %num_items% آیتم(ها) هستید.',
        'content' => 'این عملیات غیرقابل بازگشت است.',
        'action' => 'ادامه دهید',
    ],

    'delete_modal' => [
        'title' => 'واقعا می‌خواهید این آیتم را حذف کنید؟',
        'content' => 'این عملیات غیرقابل بازگشت است.',
    ],

    'filter' => [
        'title' => 'فیلترها',
        'button.clear' => 'پاک کردن',
        'button.apply' => 'درخواست دادن',
        'label.is_equal_to' => 'مساوی با',
        'label.is_not_equal_to' => 'نامساوی با',
        'label.is_greater_than' => 'بزرگتر از',
        'label.is_greater_than_or_equal_to' => 'بزرگتر یا مساوی با',
        'label.is_less_than' => 'کوچکتر از',
        'label.is_less_than_or_equal_to' => 'کوچکتر یا مساوی با',
        'label.is_between' => 'در بین',
        'label.contains' => 'شامل',
        'label.not_contains' => 'شامل نمی‌شود',
        'label.starts_with' => 'شروع می‌شود با',
        'label.ends_with' => 'پایان می‌یابد با',
        'label.exactly' => 'عین عبارت',
        'label.not_exactly' => 'عین عبارت نباشد',
        'label.is_same' => 'یکسان باشد',
        'label.is_not_same' => 'یکسان نباشد',
        'label.is_after' => 'بعد از آن',
        'label.is_after_or_same' => 'بعد یا همان باشد',
        'label.is_before' => 'قبل از آن',
        'label.is_before_or_same' => 'قبل یا همان باشد',
    ],

    'form' => [
        'are_you_sure' => 'شما تغییرات ایجاد شده در این فرم را ذخیره نکرده‌اید.',
        'tab.error_badge_title' => 'یک ورودی نامعتبر|%count% ورودی نا معتبر',
        'slug.confirm_text' => 'اگر نامک (slug) را تغییر دهید، امکان خراب شدن پیوندهای صفحات دیگر وجود دارد.',
    ],

    'user' => [
        'logged_in_as' => 'ورود به عنوان',
        'unnamed' => 'کاربر بدون نام',
        'anonymous' => 'کاربر ناشناس',
        'sign_out' => 'خروج',
        'exit_impersonation' => 'خروج از جعل هویت',
    ],

    'login_page' => [
        'username' => 'نام کاربری',
        'password' => 'رمزعبور',
        'sign_in' => 'ورود',
        'forgot_password' => 'کلمه عبور خود را فراموش کرده‌اید؟',
        'remember_me' => 'مرا به خاطر بسپار',
    ],

    'exception' => [
        'entity_not_found' => 'این آیتم دیگر در دسترس نیست',
        'entity_remove' => 'این آیتم را نمی توان حذف کرد زیرا آیتم‌های دیگر به آن وابسته هستند.',
        'forbidden_action' => 'عملیات درخواستی در مورد این آیتم قابل انجام نیست.',
        'insufficient_entity_permission' => 'شما اجازه دسترسی به این آیتم را ندارید.',
    ],

    'autocomplete' => [
        'no-results-found' => 'هیچ نتیجه‌ای یافت نشد',
        'no-more-results' => 'نتیجه دیگری وجود ندارد',
        'loading-more-results' => 'در حال بارگذاری نتایج بیشتر…',
    ],
];
