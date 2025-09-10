<?php

return [
    // Email Alert Messages
    'registration_approved' => 'Registration Approved',
    'appointment_approved' => 'Appointment Approved',
    'payment_proof_document_approved' => 'Payment Proof Document Approved',
    'delivery_appointment_rejected' => 'Delivery Appointment Rejected',
    'registration_referred_back' => 'Registration Referred Back',
    'unable_to_send' => 'Unable to send the email',
    
    // Email Body Messages
    'kyc_approved_body' => '<p>Dear Supplier, <br /></p><p>Please be informed that your KYC has been approved. <br><br> Thank You. </p>',
    'appointment_approved_body' => '<p>Dear Supplier, <br /></p><p>Please be informed that your appointment has been approved. <br><br> Thank You. </p>',
    'kyc_referred_back_body' => '<p>Dear Supplier,</p><p>Please be informed that your KYC form has been referred back by :empName, for the following reason.</p><p>Reason : <b>:rejectedComments</b></p><p>Please click on the "Amend" button to do the changes into KYC and resubmit for approval.</p><p><a href=":loginLink">Supplier Portal Login</a></p><p>Click the above link to login to system. Thank You.</p>',
    'delivery_appointment_rejected_body' => '<p>Dear Supplier,</p><p>Please be informed that your delivery appointment has been rejected for below reason by :empName.<br><br> :rejectedComments.<br><br> Thank You.</p>',
    
    // Document Status Messages
    'is_fully_approved' => ':attribute is fully approved',
    'level_approved_sent_next' => ':attribute Level :level is approved and sent to next level approval',
    'level_approved_sent_next_body' => ':attribute Level :level is approved and sent to next level approval to below employees <br>:nextApproveNameList',
    'is_rejected' => ':attribute is rejected for below reason by :empName<br> :rejectedComments',
    'pending_approval' => 'Pending :documentDescription approval :documentCode',
    
    // Additional Email Messages
    'new_request_approved' => 'A new request :requestCode is approved.',
    'new_request_approved_body' => '<p>A new request :requestCode is approved. Please process the order.</p>',
    'is_pending_approval' => ':attribute is pending for your approval.',
    'click_here_to_approve' => 'Click here to approve',
    'level_approved_pending' => ':attribute Level :level is approved and pending for your approval',
    'level_approved_pending_body' => '<p>:attribute Level :level is approved and pending for your approval. <br><br><a href=":redirectUrl">Click here to approve</a></p>',
    'pending_approval_body' => '<p>:attribute is pending for your approval. <br><br><a href=":redirectUrl">Click here to approve</a></p>',
    
    // Tender/Request Messages
    'tender_title' => '<b>:type Title :</b> :title',
    'tender_description' => '<b>:type Description :</b> :description',
    
    // Email Helper Messages
    'hi' => 'Hi',
    'footer_save_paper' => 'SAVE PAPER - THINK BEFORE YOU PRINT!',
    'footer_auto_generated' => 'This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.',
    'employee_not_found' => 'Employee Not Found',
    'company_not_found' => 'Company Not Found',
    'document_not_found' => 'Document Not Found',
    'document_id_not_found' => 'Document ID not found',
    'successfully_inserted' => 'Successfully Inserted',
    'unverified_email_message' => 'Notification cannot be sent to the following approvers regarding pending approval due to unverified email addresses.',
];

