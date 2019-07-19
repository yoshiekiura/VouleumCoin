<?php

return [
    'email_exist' => 'Email is already exist!',
    'invalid_form' => 'Invalid form data!',
    'wrong' => 'Something is wrong!',
    'nothing' => 'Nothing to do!',
    'agree' => 'You should agree our terms and policy.',
    'errors' => 'An error occurred. Please try again.',
    'login' => [
        'email_verify' => 'Please login to verify you email address.',
        'inactive' => 'Your account may inactive or suspended. Please contact us if something wrong.',
    ],
    'register' => [
        'success' => [
            'heading' => 'Thank you!',
            'subhead' => 'Your sign-up process is almost done.',
            'msg' => 'Please check your email and verify your account.',
        ],
    ],
    'verify' => [
        'verified' => 'Email address is already verified.',
        'not_found' => 'User Account is not found!',
        'expired' => 'Your verification link is expired!',
        'invalid' => 'Your verification link is invalid!',
        'confirmed' => 'Your email is verified now!',
        'success' => [
            'heading' => 'Congratulations!',
            'subhead' => "You've successfully verified your email address and your account is now active.",
            'msg' => 'Please sign-in to start token purchase.',
        ],
    ],
    'trnx' => [
        'created' => 'Transaction successful, You will redirect to payment page.',
        'wrong' => 'Something is wrong!',
        'canceled' => 'Transaction failed! Try again.',
        'notfound' => 'Transaction id is not found',
        'reviewing' => 'We are reviewing your payment!',
        'canceled_own' => 'You had canceled your order',
        'require_currency' => "Currency is required.",
        'require_token' => "Token amount is required!.",
        'minimum_token' => "You have to purchase more than 1 token.",
        'purchase_token' => "Tokens Purchase",
        'payments' => [
            'not_available' => 'Sorry! Currently payment method not available in your selected currency!',
        ],
        'manual' => [
            'success' => 'Transaction successful!',
            'failed' => 'Transaction Failed!',
        ],
        'admin' => [
            'approved' => 'Transaction approved and token added to user.',
            'canceled' => 'Transaction canceled.',
            'deleted' => 'Transaction Deleted.',
            'already_deleted' => "This transaction is already deleted.",
            'already_approved' => "This transaction is already approved.",
        ],
    ],
    'token' => [
        'success' => 'Token added to the user account!',
        'failed' => 'Failed to add token!',
    ],
    'insert' => [
        'success' => ':what insert successful!',
        'warning' => 'Something is wrong!',
        'failed' => ':what insert failed!',
    ],
    'stage' => [
        'expired' => 'Sorry, this stage is expired!',
        'inactive' => 'Currently no active stage found!',
        'notice' => 'Please create a new stage or update stage date, because this stage is expired!',
        'upcoming' => 'Stage will start at :time',
        'delete_failed' => "You can not remove the last stage.",
    ],

    'update' => [
        'success' => ':what has been updated!',
        'warning' => 'Something is wrong!',
        'failed' => ':what updating failed!',
    ],
    'password' => [
        'old_err' => 'Your old password is incorrect.',
        'success' => 'Password successfully changed!',
        'changed' => 'We have sent a verification code to your email please confirm and change.',
        'failed' => 'Varification link has expired!!! try again',
        'token' => 'Invalid link/token!!! try again',
    ],
    'delete' => [
        'delete' => ':what is deleted!',
        'delete_failed' => ':what is deletion failed!',
    ],
    'kyc' => [
        'approved' => "KYC application approved successfully!",
        'missing' => "KYC application is missing!",
        'rejected' => "KYC application is rejected!",
        'wait' => "Your KYC Application is placed, please wait for our review.",
        'mandatory' => "Identity verification (KYC/AML) is mandatory to participate in our token sale.",
        'forms' => [
            'submitted' => "You have successfully submitted your application for identity verification.",
            'failed' => "We weren't able to process the application submission for identity verification. Please reload this page and fill the form again and submit. ",
        ],
    ],
    'upload' => [
        'success' => ':what has been uploaded!',
        'warning' => 'Something is wrong!',
        'invalid' => 'This type of file is not supported!',
        'failed' => ':what uploading failed!',
    ],
    'invalid' => [
        'address' => 'Enter a valid wallet address.',
        'address_is' => 'Enter a valid :is wallet address.',
        'social' => 'Sorry, Social login is not available now.',
    ],
    'mail' => [
        'send' => 'Email has been send successfully.',
        'failed' => 'Failed to send email.',
    ],
    'wallet' => [
        'change' => 'Wallet address change request submitted.',
        'cancel' => 'Wallet address change request is canceled.',
        'approved' => 'Wallet address change request is approved.',
        'failed' => 'Wallet address change request is failed.',
    ],

    'email' => [
        'reset' => 'Somthing is wrong !! We are unable to send reset link to your email. Please! contact with administrator via :email.',
        'verify' => 'Somthing is wrong !! We are unable to send the verification link to your email. Please! contact with administrator via :email.',
        'password_change' => 'Somthing is wrong !! We are unable to send the confirmation link to your email. Please! contact with administrator via :email.',
        'kyc' => 'but email was not send. something wrong with your email credential. Please! check your email setting',
        'token_update' => 'but email was not send. something wrong with your email credential. Please! check your email setting',
        'user_add' => 'but email was not send. something wrong with your email credential. Please! check your email setting',
        'failed' => 'Email was not send. something wrong with your email credential. Please! check your email setting',
    ],

    'unique_email' => 'Email address should be unique!',
    'something_wrong' => 'Something wrong in form submission!',
    'ico_not_setup' => 'ICO Sales opening soon, Please check after sometimes.',
    'demo_user' => 'Your action can\'t perform as you login with a Demo Account. For full-access, please send an email at info@softnio.com.',
    'stage_update' => 'Successfully :status the stage!!',
];
