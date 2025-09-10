<?php

return [
    // Email Alert Messages
    'registration_approved' => 'تم الموافقة على التسجيل',
    'appointment_approved' => 'تم الموافقة على الموعد',
    'payment_proof_document_approved' => 'تم الموافقة على وثيقة إثبات الدفع',
    'delivery_appointment_rejected' => 'تم رفض موعد التسليم',
    'registration_referred_back' => 'تم إرجاع التسجيل',
    'unable_to_send' => 'غير قادر على إرسال البريد الإلكتروني',
    
    // Email Body Messages
    'kyc_approved_body' => '<p>عزيزي المورد، <br /></p><p>نحيطكم علماً بأنه تم الموافقة على معرفك. <br><br> شكراً لكم. </p>',
    'appointment_approved_body' => '<p>عزيزي المورد، <br /></p><p>نحيطكم علماً بأنه تم الموافقة على موعدكم. <br><br> شكراً لكم. </p>',
    'kyc_referred_back_body' => '<p>عزيزي المورد،</p><p>نحيطكم علماً بأنه تم إرجاع نموذج معرفك من قبل :empName، للأسباب التالية.</p><p>السبب : <b>:rejectedComments</b></p><p>الرجاء النقر على زر "تعديل" لإجراء التغييرات في معرفك وإعادة تقديمه للموافقة.</p><p><a href=":loginLink">تسجيل دخول بوابة المورد</a></p><p>انقر على الرابط أعلاه لتسجيل الدخول إلى النظام. شكراً لكم.</p>',
    'delivery_appointment_rejected_body' => '<p>عزيزي المورد،</p><p>نحيطكم علماً بأنه تم رفض موعد التسليم للأسباب التالية من قبل :empName.<br><br> :rejectedComments.<br><br> شكراً لكم.</p>',
    
    // Document Status Messages
    'is_fully_approved' => ':attribute تم الموافقة عليه بالكامل',
    'level_approved_sent_next' => ':attribute المستوى :level تم الموافقة عليه وإرساله للموافقة على المستوى التالي',
    'level_approved_sent_next_body' => ':attribute المستوى :level تم الموافقة عليه وإرساله للموافقة على المستوى التالي للموظفين التاليين <br>:nextApproveNameList',
    'is_rejected' => ':attribute تم رفضه للأسباب التالية من قبل :empName<br> :rejectedComments',
    'pending_approval' => 'في انتظار موافقة :documentDescription :documentCode',
    
    // Additional Email Messages
    'new_request_approved' => 'تم الموافقة على طلب جديد :requestCode.',
    'new_request_approved_body' => '<p>تم الموافقة على طلب جديد :requestCode. الرجاء معالجة الطلب.</p>',
    'is_pending_approval' => ':attribute في انتظار موافقتك.',
    'click_here_to_approve' => 'انقر هنا للموافقة',
    'level_approved_pending' => ':attribute المستوى :level تم الموافقة عليه وهو في انتظار موافقتك',
    'level_approved_pending_body' => '<p>:attribute المستوى :level تم الموافقة عليه وهو في انتظار موافقتك. <br><br><a href=":redirectUrl">انقر هنا للموافقة</a></p>',
    'pending_approval_body' => '<p>:attribute في انتظار موافقتك. <br><br><a href=":redirectUrl">انقر هنا للموافقة</a></p>',
    
    // Tender/Request Messages
    'tender_title' => '<b>:type العنوان :</b> :title',
    'tender_description' => '<b>:type الوصف :</b> :description',
    
    // Email Helper Messages
    'hi' => 'مرحباً',
    'footer_save_paper' => 'وفر الورق - فكر قبل الطباعة!',
    'footer_auto_generated' => 'هذا بريد إلكتروني مُولد تلقائياً. الرجاء عدم الرد على هذا البريد الإلكتروني لأننا لا نراقب هذا الصندوق الوارد.',
    'employee_not_found' => 'الموظف غير موجود',
    'company_not_found' => 'الشركة غير موجودة',
    'document_not_found' => 'الوثيقة غير موجودة',
    'document_id_not_found' => 'معرف الوثيقة غير موجود',
    'successfully_inserted' => 'تم الإدراج بنجاح',
    'unverified_email_message' => 'لا يمكن إرسال الإشعار إلى الموافقين التاليين بخصوص الموافقة المعلقة بسبب عناوين البريد الإلكتروني غير المُتحقق منها.',
];

