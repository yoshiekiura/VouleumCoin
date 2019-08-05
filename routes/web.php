<?php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middle-ware group. Now create something great!
|
*/

if(application_installed()){
    Route::get('/install/final', function(){
        return redirect('/');
    });
}

// Handle Main / Route
Route::get('/', 'HomeController@home')->name('home');
Route::get('/locale', 'PublicController@set_lang')->name('language');

// Authenticates Routes
Route::get('/auth/{service}', 'Auth\SocialAuthController@redirect')->name('social.login');
Route::get('/auth/{service}/callback', 'Auth\SocialAuthController@callback')->name('social.login.callback');
Route::post( '/auth/social/register', 'Auth\SocialAuthController@register' )->name('social.register');

// Authenticates Routes
Auth::routes();
Route::get('verify/', 'Auth\VerifyController@index')->name('verify');
Route::get('verify/resend', 'Auth\VerifyController@resend')->name('verify.resend');
Route::get('verify/{id}/{token}', 'Auth\VerifyController@verify')->name('verify.email');
Route::get('verify/success', 'Auth\LoginController@verified')->name('verified');
//register
Route::get('register/success', 'Auth\LoginController@registered')->name('registered');

Route::any('log-out', 'Auth\LoginController@logout')->name('log-out');

// if(is_maintenance()){
Route::get('admin/login', 'Auth\LoginController@showLoginForm')->name('admin.login');
Route::post('admin/login', 'Auth\LoginController@login');
Route::post('admin/logout', 'Auth\LoginController@logout')->name('admin.logout');
// }


// User Routes------------------------------------------------------------------------
Route::prefix('user')->middleware(['auth', 'user', 'verify_user'])->name('user.')->group(function () {
    Route::get('/', 'User\UserController@index')->name('home');
    Route::get('/account', 'User\UserController@account')->name('account');
    Route::get('/referrals', 'User\UserController@referrals')->name('referrals');
    Route::get('/account/activity', 'User\UserController@account_activity')->name('account.activity');
    Route::get('/contribute', 'User\TokenController@index')->name('token');
    Route::get('/transactions', 'User\TransactionController@index')->name('transactions');
    Route::get('/kyc', 'User\KycController@index')->name('kyc');
    Route::get('/kyc/application', 'User\KycController@application')->name('kyc.application');
    Route::get('/kyc/application/view', 'User\KycController@view')->name('kyc.application.view');
    Route::get('/kyc-list/documents/{file}/{doc}', 'User\KycController@get_documents')->middleware('ico')->name('kycs.file');
    Route::get('/password/confirm/{token}', 'User\UserController@password_confirm')->name('password.confirm');

    // User Ajax Request
    Route::name('ajax.')->prefix('ajax')->group(function () {
        Route::post('/account/wallet-form', 'User\UserController@get_wallet_form')->name('account.wallet');
        Route::post('/account/update', 'User\UserController@account_update')->name('account.update')->middleware('demo_user');
        Route::post('/contribute/access', 'User\TokenController@access')->name('token.access');
        Route::post('/contribute/payment/manual', 'User\TokenController@store')->name('payment.manual')->middleware('demo_user');
        Route::post('/contribute/payment/action', 'User\TokenController@update')->name('payment.update')->middleware('demo_user');
        Route::post('/transactions/delete/{id}', 'User\TransactionController@destroy')->name('transactions.delete');
        Route::post('/transactions/view', 'User\TransactionController@show')->name('transactions.view');
        Route::post('/kyc/submit', 'User\KycController@submit')->name('kyc.submit');
        Route::post('/account/activity', 'User\UserController@account_activity_delete')->name('account.activity.delete');
    });
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', 'Admin\AdminController@index')->name('home');
    Route::get('/profile', 'Admin\AdminController@profile')->middleware('ico')->name('profile');
    Route::get('/profile/activity', 'Admin\AdminController@activity')->middleware('ico')->name('profile.activity');
    Route::get('/transactions/{state}', 'Admin\TransactionController@index')->middleware('ico')->name('transactions.withState');
    Route::get('/stages/settings', 'Admin\IcoController@settings')->middleware('ico')->name('stages.settings');
    Route::get('/pages', 'Admin\PageController@index')->middleware('ico')->name('pages');
    Route::get('/settings', 'Admin\SettingController@index')->middleware(['ico', 'super_admin'])->name('settings');
    Route::get('/settings/email', 'Admin\EmailSettingController@index')->middleware(['ico', 'super_admin'])->name('settings.email');
    Route::get('/payment-methods', 'Admin\PaymentMethodController@index')->middleware(['ico', 'super_admin'])->name('payments.setup');
    Route::get('/stages', 'Admin\IcoController@index')->middleware('ico')->name('stages');
    Route::get('/stages/{id}', 'Admin\IcoController@edit_stage')->middleware('ico')->name('stages.edit');
    Route::get('/users', 'Admin\UsersController@index')->middleware('ico')->name('users');
    Route::get('/users/wallet/change-request', 'Admin\UsersController@wallet_change_request')->middleware('ico')->name('users.wallet.change');
    Route::get('/kyc-list', 'Admin\KycController@index')->middleware('ico')->name('kycs');
    Route::get('/transactions', 'Admin\TransactionController@index')->middleware('ico')->name('transactions');
    Route::get('/kyc-list/documents/{file}/{doc}', 'Admin\KycController@get_documents')->middleware('ico')->name('kycs.file');
    Route::get('/transactions/view/{id}', 'Admin\TransactionController@show')->name('transactions.view');
    Route::get('/users/view/{id}/{type}', 'Admin\UsersController@show')->name('users.view');
    Route::get('/kyc/view/{id}/{type}', 'Admin\KycController@show')->name('kyc.view');
    Route::get('/pages/{slug}', 'Admin\PageController@edit')->middleware('ico')->name('pages.edit');
    Route::get('/password/confirm/{token}', 'Admin\AdminController@password_confirm')->name('password.confirm');

    /*admin ajax*/
    Route::name('ajax.')->prefix('ajax')->middleware(['ico'])->group(function () {
        Route::post('/users/view', 'Admin\UsersController@status')->name('users.view')->middleware('demo_user');
        Route::post('/users/delete/all', 'Admin\UsersController@delete_unverified_user')->name('users.delete')->middleware('demo_user');
        Route::post('/users/email/send', 'Admin\UsersController@send_email')->name('users.email');
        Route::post('/users/insert', 'Admin\UsersController@store')->middleware(['super_admin', 'demo_user'])->name('users.add');
        Route::post('/profile/update', 'Admin\AdminController@profile_update')->name('profile.update')->middleware('demo_user');
        Route::post('/users/wallet/action', 'Admin\UsersController@wallet_change_request_action')->name('users.wallet.action');
        Route::post('/profile/activity', 'Admin\AdminController@activity_delete')->name('profile.activity.delete');
        Route::post('/payment-methods/view', 'Admin\PaymentMethodController@show')->middleware('super_admin')->name('payments.view');
        Route::post('/payment-methods/update', 'Admin\PaymentMethodController@update')->middleware(['super_admin', 'demo_user'])->name('payments.update');
        Route::post('/kyc/view', 'Admin\KycController@ajax_show')->name('kyc.ajax_show');
        Route::post('/stages/update', 'Admin\IcoController@update')->name('stages.update')->middleware('demo_user');
        Route::post('/stages/meta/update', 'Admin\IcoController@update_options')->name('stages.meta.update')->middleware('demo_user');
    Route::post('/stages/active', 'Admin\IcoController@active')->middleware('ico')->name('active.stage')->middleware('demo_user');
    Route::post('/stages/pause', 'Admin\IcoController@pause')->middleware('ico')->name('pause.stage')->middleware('demo_user');
        Route::post('/stages/settings/update', 'Admin\IcoController@update_settings')->name('stages.settings.update')->middleware('demo_user');
        Route::post('/kyc/update', 'Admin\KycController@update')->name('kyc.update')->middleware('demo_user');
        Route::post('/transactions/update', 'Admin\TransactionController@update')->name('transactions.update')->middleware('demo_user');

        Route::post('/transactions/adjust', 'Admin\TransactionController@adjustment')->name('transactions.adjustement');
        Route::post('/settings/email/template/view', 'Admin\EmailSettingController@show_template')->middleware('super_admin')->name('settings.email.template.view');
        Route::post('/transactions/view', 'Admin\TransactionController@show')->name('transactions.view');
        Route::post('/transactions/insert', 'Admin\TransactionController@store')->name('transactions.add')->middleware('demo_user');
        Route::post('/pages/upload', 'Admin\PageController@upload_zone')->name('pages.upload');
        Route::post('/pages/view', 'Admin\PageController@show')->name('pages.view');
        Route::post('/pages/update', 'Admin\PageController@update')->name('pages.update')->middleware('demo_user');
        Route::post('/settings/update', 'Admin\SettingController@update')->middleware(['super_admin','demo_user'])->name('settings.update');
        Route::post('/settings/email/update', 'Admin\EmailSettingController@update')->middleware(['super_admin', 'demo_user'])->name('settings.email.update');
        Route::post('/settings/email/template/update', 'Admin\EmailSettingController@update_template')->middleware(['super_admin', 'demo_user'])->name('settings.email.template.update');
    });

    //Clear Cache facade value:
    Route::get('/clear', function () {
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('view:clear');

        $data = ['msg' => 'success', 'message' => 'Cache Cleared and Optimized!'];

        if (request()->ajax()) {
            return response()->json($data);
        }
        return back()->with([$data['msg'] => $data['message']]);
    })->name('clear.cache');
});

Route::name('public.')->group(function () {
    Route::get('/insert/database', 'PublicController@database')->name('database');
    Route::get('/kyc-application', 'PublicController@kyc_application')->name('kyc');
    Route::get('/invite', 'PublicController@referral')->name('referral');
    Route::post('/kyc-application/file-upload', 'User\KycController@upload')->name('kyc.file.upload');
    Route::post('/kyc-application/submit', 'User\KycController@submit')->name('kyc.submit');

    Route::get('white-paper', function () {
        $filename = get_setting('site_white_paper');
        $path = storage_path('app/public/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        $file = \File::get($path);
        $type = \File::mimeType($path);
        $response = response($file, 200)->header("Content-Type", $type);
        return $response;
    })->name('white.paper');

    Route::get('/{slug}', 'PublicController@site_pages')->name('pages');
});

// Ajax Routes
Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::post('/kyc/file-upload', 'User\KycController@upload')->name('kyc.file.upload');
});
