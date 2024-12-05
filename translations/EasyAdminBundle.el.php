<?php

return [
    'page_title' => [
        'dashboard' => 'Πίνακας ελέγχου',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Επεξεργασία %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Δημιουργία %entity_label_singular%',
        'exception' => 'Λάθος|Λάθοι',
    ],

    'datagrid' => [
        'hidden_results' => 'Ορισμένα αποτελέσματα δεν μπορούν να εμφανιστούν επειδή δεν έχετε αρκετά δικαιώματα',
        'no_results' => 'Δεν βρέθηκαν αποτελέσματα.',
    ],

    'paginator' => [
        'first' => 'Πρώτη',
        'previous' => 'Προηγούμενη',
        'next' => 'Επόμενη',
        'last' => 'Τελευταία',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> από <strong>%results%</strong>',
        'results' => '{0} Δεν βρέθηκαν εγγραφές |{1} <strong>1</strong> εγγραφή|]1,Inf] <strong>%count%</strong> εγγραφές',
    ],

    'label' => [
        'true' => 'Ναι',
        'false' => 'Όχι',
        'empty' => 'Άδειο',
        'null' => 'Κενό',
        'object' => 'Αντικείμενο PHP',
        'inaccessible' => 'Μη προσβάσιμο',
        'inaccessible.explanation' => 'Δεν υπάρχει μέθοδος ανάγνωσης (getter) για αυτό το πεδίο ή η ιδιότητα αυτή δεν είναι προσβάσιμη',
        'form.empty_value' => 'Καμία',
    ],

    'field' => [
        'code_editor.view_code' => 'Προβολή κώδικα',
        'text_editor.view_content' => 'Προβολή περιεχομένου',
    ],

    'action' => [
        'entity_actions' => 'Ενέργειες',
        'new' => 'Δημιουργία %entity_label_singular%',
        'search' => 'Αναζήτηση',
        'detail' => 'Εμφάνιση',
        'edit' => 'Επεξεργασία',
        'delete' => 'Διαγραφή',
        'cancel' => 'Άκυρο',
        'index' => 'Επιστροφή στην λίστα',
        'deselect' => 'Αποεπιλογή',
        'add_new_item' => 'Δημιουργία νέου αντικειμένου',
        'remove_item' => 'Αφαίρεση αντικειμένου',
        'choose_file' => 'Επιλογή αρχείου',
        'close' => 'Κλείσιμο',
        'create' => 'Δημιουργία',
        'create_and_add_another' => 'Δημιουργία και προσθήκη άλλου',
        'create_and_continue' => 'Δημιουργία και συνέχιση επεξεργασίας',
        'save' => 'Αποθήκευση αλλαγών',
        'save_and_continue' => 'Αποθήκευση και συνέχιση επεξεργασία',
    ],

    'batch_action_modal' => [
        'title' => 'Θα εφαρμόσετε την ενέργεια "%action_name%" στο %num_items% αντικείμενο(α).',
        'content' => 'Αυτή η ενέργεια δεν αναιρείται.',
        'action' => 'Συνεχίστε.',
    ],

    'delete_modal' => [
        'title' => 'Θέλετε σίγουρα να διαγράψετε αυτό το αντικείμενο;',
        'content' => 'Αυτή η ενέργεια δεν αναιρείται.',
    ],

    'filter' => [
        'title' => 'Φίλτρα',
        'button.clear' => 'Καθαρισμός',
        'button.apply' => 'Εφαρμογή',
        'label.is_equal_to' => 'είναι ίσο με',
        'label.is_not_equal_to' => 'δεν είναι ίσο με',
        'label.is_greater_than' => 'είναι μεγαλύτερο από',
        'label.is_greater_than_or_equal_to' => 'είναι μεγαλύτερο ή ίσο με',
        'label.is_less_than' => 'είναι μικρότερο από',
        'label.is_less_than_or_equal_to' => 'είναι μικρότερο ή ίσο με',
        'label.is_between' => 'είναι μεταξύ',
        'label.contains' => 'περιέχει',
        'label.not_contains' => 'δεν περιέχει',
        'label.starts_with' => 'ξεκινάει με',
        'label.ends_with' => 'τελειώνει με',
        'label.exactly' => 'ακριβώς',
        'label.not_exactly' => 'όχι ακριβώς',
        'label.is_same' => 'είναι ίδιο',
        'label.is_not_same' => 'δεν είναι το ίδιο',
        'label.is_after' => 'είναι μετά',
        'label.is_after_or_same' => 'είναι μετά ή το ίδιο',
        'label.is_before' => 'είναι πριν',
        'label.is_before_or_same' => 'είναι πριν ή το ίδιο',
    ],

    'form' => [
        'are_you_sure' => 'Δεν έχετε αποθηκεύσει τις αλλαγές που έχετε κάνει στην φόρμα.',
        'tab.error_badge_title' => 'Μη αποδεκτή τιμή|%count% μη αποδεκτές τιμές',
        'slug.confirm_text' => 'Εάν αλλάξετε τον εναλλακτικό τίτλο, μπορείτε να διακόψετε συνδέσμους σε άλλες σελίδες',
    ],

    'user' => [
        'logged_in_as' => 'Συνδεδεμένος ως',
        'unnamed' => 'Χρήστης δίχως όνομα',
        'anonymous' => 'Ανώνυμος Χρήστης',
        'sign_out' => 'Αποσύνδεση',
        'exit_impersonation' => 'Διακοπή προσωποποίησης',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Εμφάνιση',
            'light' => 'Ανοιχτόχρωμη',
            'dark' => 'Σκούρα',
            'auto' => 'Αυτόματα',
        ],
        'locale' => 'Γλώσσα',
    ],

    'login_page' => [
        'username' => 'Όνομα χρήστη',
        'password' => 'Κωδικός πρόσβασης',
        'sign_in' => 'Συνδεθείτε',
        'forgot_password' => 'Ξεχάσατε τον κωδικό σας?',
        'remember_me' => 'Να με θυμάσαι',
    ],

    'exception' => [
        'entity_not_found' => 'Αυτό το αντικείμενο δεν είναι πλέον διαθέσιμο.',
        'entity_remove' => 'Το αντικείμενο δεν είναι δυνατόν να διαγραφεί διότη υπάρχουν αντικείμενο που βασίζονται σε αυτό.',
        'forbidden_action' => 'Η ενέργεια αυτή είναι αδύνατον να εφαρμοστεί σε αυτό το αντικείμενο.',
        'insufficient_entity_permission' => 'Δεν έχετε δικαιώματα πρόσβασης σε αυτό το αντικείμενο',
    ],

    'autocomplete' => [
        'no-results-found' => 'Δεν βρέθηκαν αποτελέσματα',
        'no-more-results' => 'Δεν υπάρχουν άλλα αποτελέσματα',
        'loading-more-results' => 'Φόρτωση περισσότερων αποτελεσμάτων…',
    ],
];
