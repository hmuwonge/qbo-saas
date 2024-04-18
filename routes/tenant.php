<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AnnouncementController;
use Modules\EfrisReports\Http\Controllers\EfrisController;
use Modules\Goods\Http\Controllers\GoodsController;
use Modules\Invoices\Http\Controllers\InvoicesController;
use Modules\Invoices\Http\Controllers\ValidationsController;
use Modules\Purchases\Http\Controllers\PurchasesController;
use Modules\QuickbooksDashboard\Http\Controllers\QuickbooksDashboardController;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConversionsController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\LoginSecurityController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialLoginController;
use App\Http\Controllers\Admin\LandingController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\SmsTemplateController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\NotificationsSettingController;
use App\Http\Controllers\Admin\DocumentGenratorController;
use App\Http\Controllers\Admin\DocumentMenuController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\PageSettingController;
use App\Http\Controllers\Admin\OfflineRequestController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\Payment\MolliePaymentController;
use App\Http\Controllers\Admin\Payment\FlutterwaveController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    require __DIR__ . '/auth.php';
    require __DIR__.'/auto_sync.php';
    require __DIR__.'/quickbooks.php';


    Route::get('/tenant-impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    });

    Route::group(['middleware' => ['auth', 'Setting', '2fa', 'Upload']], function () {
        Route::get('show-announcement-list/', [AnnouncementController::class, 'showAnnouncementList'])->name('show.announcement.list');
        Route::get('show-announcement/{id}', [AnnouncementController::class, 'showAnnouncement'])->name('show.announcement');
    });
    Route::group(['middleware' => ['Setting', 'xss', 'Upload']], function () {
        Route::get('redirect/{provider}', [SocialLoginController::class, 'redirect']);
        Route::get('callback/{provider}', [SocialLoginController::class, 'callback'])->name('social.callback');

        Route::get('contactus', [LandingController::class, 'contactUs'])->name('contact.us');
        Route::get('all/faqs', [LandingController::class, 'faqs'])->name('faqs.pages');
        Route::get('terms-conditions', [LandingController::class, 'termsAndConditions'])->name('terms.and.conditions');
        Route::post('contact-mail', [LandingController::class, 'contactMail'])->name('contact.mail');

        //sms
        Route::get('sms/notice', [SmsController::class, 'smsNoticeIndex'])->name('smsindex.noticeverification');
        Route::post('sms/notice', [SmsController::class, 'smsNoticeVerify'])->name('sms.noticeverification');
        Route::get('sms/verify', [SmsController::class, 'smsIndex'])->name('smsindex.verification');
        Route::post('sms/verify', [SmsController::class, 'smsVerify'])->name('sms.verification');
        Route::post('sms/verifyresend', [SmsController::class, 'smsResend'])->name('sms.verification.resend');

        //Blogs pages
        Route::get('blog/{slug}', [PostsController::class, 'viewBlog'])->name('view.blog');
        Route::get('see/blogs', [PostsController::class, 'seeAllBlogs'])->name('see.all.blogs');

        Route::get('pages/{slug}', [LandingPageController::class, 'pageDescription'])->name('description.page');
    });

    Route::group(['middleware' => ['auth', 'Setting', 'xss', '2fa', 'verified', 'verified_phone', 'Upload']], function () {
        Route::impersonate();

// All FRIS URA DATA FROM THE EFRIS MIDDLEWARE API
        Route::group(['prefix' => 'efris-ura', 'middleware' => ['auth', 'web', 'verified']], function () {
            Route::get('fiscalised-invoices', [EfrisController::class, 'invoices'])->name('ura.invoices');
            Route::get('fiscalised-receipts', [EfrisController::class, 'receipts'])->name('ura.receipts');
            Route::get('cancel-creditnote/{id}', [EfrisController::class, 'creditNoteDetails'])->name('creditnote.cancel.view');
            Route::get('fiscal-invoice/details/{id}', [EfrisController::class, 'invoicesDetails'])->name('fiscal-invoice.preview');
            Route::get('fiscal-invoice-download/{id}', [EfrisController::class, 'actionViewInvoicePdf'])->name('invoice.download.rt');
            Route::get('fiscal-creditnote-download/{id}', [EfrisController::class, 'actionViewCreditnotePdf'])->name('creditnote.download');

            Route::get('fiscalise/{id}/{kind}', [InvoicesController::class, 'actionFiscaliseInvoice'])->name('invoice.fiscalise');

            Route::get('issued-credit-notes', [EfrisController::class, 'creditNotes'])->name('ura.creditnotes');
            Route::get('goods-services', [EfrisController::class, 'goodsAndServices2'])->name('ura.goods');
        });

        Route::prefix('efrisreports')->group(function() {
            Route::get('/', [EfrisController::class,'invoices'])->name('efris.invoices');
            Route::get('/goods-services', [EfrisController::class,'goodsAndServices2'])->name('efris.goods');
            Route::post('/goods-services', [EfrisController::class,'goodsAndServices2'])->name('efris.goods.get');
        });

        // purchases routes
        Route::group(['prefix' => 'quickbooks/purchases', 'middleware' => ['auth','xss', 'web', 'token', 'verified', 'qbo.token']], function () {
            Route::get('/', [PurchasesController::class,'index'])->name('purchases.index');
            Route::post('update-invoice-buyer-type', [PurchasesController::class, 'updatePurchaseStockInType'])->name('purchase.stockUpdate');
            Route::get('fiscalise-increase-stock/{id}', [PurchasesController::class, 'increasePurchaseStock'])->name('quickbooks.fiscalise-increase-stock');
        });

        Route::group(['prefix' => 'quickbooks/invoices', 'middleware' => ['auth', 'web', 'xss', 'verified', 'qbo.token']], function () {
            Route::get('/', [InvoicesController::class, 'index'])->name('qbo.invoices.all');
            Route::get('invoices-range/{validate}', [InvoicesController::class, 'invoicesRange'])->name('qbo.invoices.range');
            Route::get('/passed-validations', [InvoicesController::class, 'passedValidations'])->name('qbo.invoices.passed');
            Route::get('/failed-validations', [InvoicesController::class, 'failed'])->name('qbo.invoices.failed');
            Route::get('/validation-errors', [InvoicesController::class, 'errors'])->name('qbo.invoices.errors');
            Route::get('/fiscalised', [InvoicesController::class, 'fiscalised'])->name('qbo.invoices.ura');
//    Route::post('update-invoice-industry-code', [InvoicesController::class, 'updateInvoiceIndustry'])->name('update.industrycode');

            Route::get('invoice-preview/{id}', [InvoicesController::class,'actionInvoicePreview'])->name('invoice.preview');
            Route::get('invoice-sample/{id}/{invoice}', [InvoicesController::class,'actionInvoicePreview'])->name('invoices.sample');
            Route::get('invoices-sync', [InvoicesController::class,'syncInvoices'])->name('invoices.sync');
        });

        /////////////////receipts////////////////
        Route::group(['prefix' => 'quickbooks/receipts', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
            Route::get('/', [ReceiptsController::class, 'index'])->name('qbo.receipts.index');
            Route::get('/passed-validations', [ReceiptsController::class, 'passedValidations'])->name('qbo.receipts.passed');
            Route::get('/failed-validations', [ReceiptsController::class, 'failed'])->name('qbo.receipts.failed');
            Route::get('/validation-errors', [ReceiptsController::class, 'errors'])->name('qbo.receipts.errors');
            Route::get('/fiscalised', [ReceiptsController::class, 'fiscalised'])->name('qbo.receipts.ura');
            Route::post('update-invoice-industry-code', [ReceiptsController::class, 'updateInvoiceIndustry'])->name('receipts.update.industrycode');
            Route::post('update-invoice-buyer-type', [ReceiptsController::class, 'updateBuyerType'])->name('receipts.update.buyerType');

            Route::get('receipts-range/{validate}', [ReceiptsController::class, 'receiptsDateRange'])->name('qbo.receipts.range');
        });
        Route::get('validate/all/receipts', [ValidationsController::class, 'validateReceipts'])->name('receipts.validate');

//        Route::group(['prefix' => 'quickbooks/goods', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
//            Route::get('/', [GoodsController::class,'index'])->name('goods.all');//->middleware('is_connected');
//            Route::get('/not-registered', 'GoodsController@index')->name('goods.noregistered');
//            Route::get('sync-items', 'GoodsController@syncItems')->name('goods.syncItems');
//            Route::get('register-opening-stock/{id}', 'GoodsController@registerOpeningStockView')->name('quickbooks.register-stock');
//            Route::post('register-opening-stock/{id}', 'GoodsController@registerOpeningStock')->name('quickbooks.register-stock.store');
//            Route::get('/product-details/{id}', 'GoodsController@actionItemProductDetails')->name('goods.product-details');
//            Route::post('register-product/{id}', 'GoodsController@registerProductn')->name('quickbooks.register-product-efris');
//            Route::match(['get','post'],'/register-product/{id}/{redo?}', 'GoodsController@registerProduct')->name('quickbooks.register-product');
//        });

        // stock adjustments
//        Route::group(['prefix' => 'quickbooks/stockadjustments', 'middleware' => ['auth', 'web', 'token', 'verified', 'qbo.token']], function () {
//            Route::get('/', 'StockAdjustmentsController@index')->name('qbo.stockadjustments');
//            Route::get('/sync', 'StockAdjustmentsController@sync')->name('qbo.stockadjustments.sync');
//            Route::get('reduce-stock/{id}/{stock}', 'StockAdjustmentsController@actionReduceStock')->name('stockAdjust.reduce-stock');
//            Route::post('update-stockin-type', [StockAdjustmentsController::class, 'updateStockADType'])->name('update.stockInType');
//        });

        Route::group(['prefix' => 'quickbooks/invoices', 'middleware' => ['auth', 'web', 'token', 'verified']], function () {
            Route::post('update-invoice-industry-type', [InvoicesController::class, 'updateInvoiceIndustry'])->name('update.industrycode');
            Route::post('update-invoice-buyer-type', [InvoicesController::class, 'updateBuyerType'])->name('invoices.update.buyerType');
        });


        Route::group(['middleware' => ['auth', 'web', 'verified', 'qbo.token']], function () {
            Route::get('validate/invoices', [ValidationsController::class, 'validateInvoices'])->name('validate.invoices');
            Route::get('validate/purchase', [ValidationsController::class, 'syncPurchaseBills'])->name('validate.bill');
            Route::post('sync-invoices-range', [ValidationsController::class, 'validateInvoicesWithDatePeriod']);
            Route::post('sync-receipts-range', [ValidationsController::class, 'validateReceiptsWithDatePeriod']);
            Route::get('validate/creditnotes', [ValidationsController::class, 'validateCreditMemos'])->name('validateCreditMemos');

        });

        Route::prefix('quickbooks')->group(function() {
            Route::get('/', [QuickbooksDashboardController::class,'index'])->name('quickbooks.index');
        });


        // category
        Route::resource('category', CategoryController::class);
        Route::post('category-status/{id}', [CategoryController::class, 'categoryStatus'])->name('category.status');

        // activity log
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity.log.index');

        Route::resource('faqs', FaqController::class);
        Route::resource('blogs', PostsController::class)->except(['show']);
        Route::post('notification/status/{id}', [NotificationsSettingController::class, 'changeStatus'])->name('notification.status.change');
        Route::resource('support-ticket', SupportTicketController::class);
        Route::resource('email-template', EmailTemplateController::class);
        Route::resource('sms-template', SmsTemplateController::class);
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');
        Route::post('support-ticket/{id}/conversion', [ConversionsController::class, 'store'])->name('conversion.store');
        Route::resource('pagesetting', PageSettingController::class);

        // user
        Route::resource('users', UserController::class);
        Route::get('user-emailverified/{id}', [UserController::class, 'userEmailVerified'])->name('user.email.verified');
        Route::get('user-phoneverified/{id}', [UserController::class, 'userPhoneVerified'])->name('user.phone.verified');
        Route::post('user-status/{id}', [UserController::class, 'userStatus'])->name('user.status');

        // role
        Route::resource('roles', RoleController::class);
        Route::post('role-permission/{id}', [RoleController::class, 'assignPermission'])->name('role.permission');

        // home
        Route::post('change/theme/mode', [HomeController::class, 'changeThemeMode'])->name('change.theme.mode');
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::post('chart', [HomeController::class, 'chart'])->name('get.chart.data');
        Route::post('read/notification', [HomeController::class, 'readNotification'])->name('admin.read.notification');
        Route::get('sales', [HomeController::class, 'sales'])->name('sales.index');

        // coupon
        Route::get('apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');
        Route::resource('coupon', CouponController::class);
        Route::post('coupon-status/{id}', [CouponController::class, 'couponStatus'])->name('coupon.status');
        Route::get('coupon/show', [CouponController::class, 'show'])->name('coupons.show');
        Route::get('coupon/csv/upload', [CouponController::class, 'uploadCsv'])->name('coupon.upload');
        Route::post('coupon/csv/upload/store', [CouponController::class, 'uploadCsvStore'])->name('coupon.upload.store');
        Route::get('coupon/mass/create', [CouponController::class, 'massCreate'])->name('coupon.mass.create');
        Route::post('coupon/mass/store', [CouponController::class, 'massCreateStore'])->name('coupon.mass.store');

        // testimonial
        Route::resource('testimonial', TestimonialController::class);
        Route::post('testimonial-status/{id}', [TestimonialController::class, 'testimonialStatus'])->name('testimonial.status');

        //event
        Route::get('event', [EventController::class, 'index'])->name('event.index');
        Route::post('event/getdata', [EventController::class, 'getEventData'])->name('event.get.data');
        Route::get('event/create', [EventController::class, 'create'])->name('event.create');
        Route::post('event/store', [EventController::class, 'store'])->name('event.store');
        Route::get('event/edit/{event}', [EventController::class, 'edit'])->name('event.edit');
        Route::any('event/update/{event}', [EventController::class, 'update'])->name('event.update');
        Route::DELETE('event/delete/{event}', [EventController::class, 'destroy'])->name('event.destroy');

        // plans
        Route::resource('plans', PlanController::class);
        Route::get('myplans', [PlanController::class, 'myPlan'])->name('plans.myplan');
        Route::get('myplans-create', [PlanController::class, 'createMyPlan'])->name('plans.createmyplan');
        Route::get('myplans/{id}/edit', [PlanController::class, 'editMyplan'])->name('requestdomain.editplan');
        Route::post('myplan-status/{id}', [PlanController::class, 'planStatus'])->name('myplan.status');
        Route::get('payment/{code}', [PlanController::class, 'payment'])->name('payment');

        // offline request
        Route::resource('offline', OfflineRequestController::class);
        Route::get('offline-request/{id}', [OfflineRequestController::class, 'offlineRequestStatus'])->name('offline.request.status');
        Route::get('offline-request/disapprove/{id}', [OfflineRequestController::class, 'disApproveStatus'])->name('offline.disapprove.status');
        Route::post('offline-request/disapprove-update/{id}', [OfflineRequestController::class, 'offlineDisApprove'])->name('request.user.disapprove.update');
        Route::post('offline-payment', [OfflineRequestController::class, 'offlinePaymentEntry'])->name('offline.payment.request');

        //2fa
        Route::group(['prefix' => '2fa'], function () {
            Route::get('/', [LoginSecurityController::class, 'show2faForm']);
            Route::post('generateSecret', [LoginSecurityController::class, 'generate2faSecret'])->name('generate2faSecret');
            Route::post('enable2fa', [LoginSecurityController::class, 'enable2fa'])->name('enable2fa');
            Route::post('disable2fa', [LoginSecurityController::class, 'disable2fa'])->name('disable2fa');
            Route::post('2faVerify', function () {
                return redirect(route('home'));
                // return redirect(URL()->previous());
            })->name('2faVerify');
        });

        // profile
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::delete('/profile-destroy/delete', [ProfileController::class, 'destroy'])->name('profile.delete');
        Route::get('profile-status', [ProfileController::class, 'profileStatus'])->name('profile.status');
        Route::post('update-avatar', [ProfileController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('profile/basicinfo/update/', [ProfileController::class, 'BasicInfoUpdate'])->name('profile.update.basicinfo');
        Route::post('update-login-details', [ProfileController::class, 'LoginDetails'])->name('update.login.details');

        //setting
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('settings/app-name/update', [SettingsController::class, 'appNameUpdate'])->name('settings.appname.update');
        Route::post('settings/pusher-setting/update', [SettingsController::class, 'pusherSettingUpdate'])->name('settings.pusher.setting.update');
        Route::post('settings/quickbooks/update', [SettingsController::class, 'QuickbooksSettingUpdate'])->name('settings.quickbooks.update');
        Route::post('settings/s3-setting/update', [SettingsController::class, 's3SettingUpdate'])->name('settings.s3.setting.update');
        Route::post('settings/email-setting/update', [SettingsController::class, 'emailSettingUpdate'])->name('settings.email.setting.update');
        Route::post('settings/sms-setting/update', [SettingsController::class, 'smsSettingUpdate'])->name('settings.sms.setting.update');
        Route::post('settings/payment-setting/update', [SettingsController::class, 'paymentSettingUpdate'])->name('settings.payment.setting.update');
        Route::post('settings/social-setting/update', [SettingsController::class, 'socialSettingUpdate'])->name('settings.social.setting.update');
        Route::post('settings/google-calender/update', [SettingsController::class, 'GoogleCalenderUpdate'])->name('settings.google.calender.update');
        Route::post('settings/auth-settings/update', [SettingsController::class, 'authSettingsUpdate'])->name('settings.auth.settings.update');
        Route::post('test-mail', [SettingsController::class, 'testSendMail'])->name('test.send.mail');
        Route::post('ckeditor/upload', [SettingsController::class, 'upload'])->name('ckeditor.upload');
        Route::post('settings/change-domain', [SettingsController::class, 'changeDomainRequest'])->name('settings.change.domain');
        Route::get('test-mail', [SettingsController::class, 'testMail'])->name('test.mail');
        Route::post('settings/cookie-setting/update', [SettingsController::class, 'cookieSettingUpdate'])->name('settings.cookie.setting.update');
        Route::post('setting/seo/save', [SettingsController::class, 'SeoSetting'])->name('setting.seo.save');

        //froentend
        Route::group(['prefix' => 'landingpage-setting'], function () {
            Route::get('app-setting', [LandingPageController::class, 'landingPageSetting'])->name('landingpage.setting');
            Route::post('app-setting/store', [LandingPageController::class, 'appSettingStore'])->name('landing.app.store');

            // menu
            Route::get('menu-setting', [LandingPageController::class, 'menuSetting'])->name('menusetting.index');
            Route::post('menu-setting-section1/store', [LandingPageController::class, 'menuSettingSection1Store'])->name('landing.menusection1.store');
            Route::post('menu-setting-section2/store', [LandingPageController::class, 'menuSettingSection2Store'])->name('landing.menusection2.store');
            Route::post('menu-setting-section3/store', [LandingPageController::class, 'menuSettingSection3Store'])->name('landing.menusection3.store');

            // feature
            Route::get('feature-setting', [LandingPageController::class, 'featureSetting'])->name('landing.feature.index');
            Route::post('feature-setting/store', [LandingPageController::class, 'featureSettingStore'])->name('landing.feature.store');
            Route::get('feature/create', [LandingPageController::class, 'featureCreate'])->name('feature.create');
            Route::post('feature/store', [LandingPageController::class, 'featureStore'])->name('feature.store');
            Route::get('feature/edit/{key}', [LandingPageController::class, 'featureEdit'])->name('feature.edit');
            Route::post('feature/update/{key}', [LandingPageController::class, 'featureUpdate'])->name('feature.update');
            Route::get('feature/delete/{key}', [LandingPageController::class, 'featureDelete'])->name('feature.delete');

            // business growth
            Route::get('business-growth-setting', [LandingPageController::class, 'businessGrowthSetting'])->name('landing.business.growth.index');
            Route::post('business-growth-setting/store', [LandingPageController::class, 'businessGrowthSettingStore'])->name('landing.business.growth.store');

            Route::get('business-growth/create', [LandingPageController::class, 'businessGrowthCreate'])->name('business.growth.create');
            Route::post('business-growth/store', [LandingPageController::class, 'businessGrowthStore'])->name('business.growth.store');
            Route::get('business-growth/edit/{key}', [LandingPageController::class, 'businessGrowthEdit'])->name('business.growth.edit');
            Route::post('business-growth/update/{key}', [LandingPageController::class, 'businessGrowthUpdate'])->name('business.growth.update');
            Route::get('business-growth/delete/{key}', [LandingPageController::class, 'businessGrowthDelete'])->name('business.growth.delete');

            Route::get('business-growth-view/create', [LandingPageController::class, 'businessGrowthViewCreate'])->name('business.growth.view.create');
            Route::post('business-growth-view/store', [LandingPageController::class, 'businessGrowthViewStore'])->name('business.growth.view.store');
            Route::get('business-growth-view/edit/{key}', [LandingPageController::class, 'businessGrowthViewEdit'])->name('business.growth.view.edit');
            Route::post('business-growth-view/update/{key}', [LandingPageController::class, 'businessGrowthViewUpdate'])->name('business.growth.view.update');
            Route::get('business-growth-view/delete/{key}', [LandingPageController::class, 'businessGrowthViewDelete'])->name('business.growth.view.delete');

            //Footer
            Route::get('footer-setting', [LandingPageController::class, 'footerSetting'])->name('landing.footer.index');
            Route::post('footer-setting/store', [LandingPageController::class, 'footerSettingStore'])->name('landing.footer.store');

            Route::get('main/menu/create', [LandingPageController::class, 'footerMainMenuCreate'])->name('footer.main.menu.create');
            Route::post('main/menu/store', [LandingPageController::class, 'footerMainMenuStore'])->name('footer.main.menu.store');
            Route::get('main/menu/edit/{id}', [LandingPageController::class, 'footerMainMenuEdit'])->name('footer.main.menu.edit');
            Route::post('main/menu/update/{id}', [LandingPageController::class, 'footerMainMenuUpdate'])->name('footer.main.menu.update');
            Route::get('main/menu/delete/{id}', [LandingPageController::class, 'footerMainMenuDelete'])->name('footer.main.menu.delete');

            Route::get('sub/menu/create', [LandingPageController::class, 'footerSubMenuCreate'])->name('footer.sub.menu.create');
            Route::post('sub/menu/store', [LandingPageController::class, 'footerSubMenuStore'])->name('footer.sub.menu.store');
            Route::get('sub/menu/edit/{id}', [LandingPageController::class, 'footerSubMenuEdit'])->name('footer.sub.menu.edit');
            Route::post('sub/menu/update/{id}', [LandingPageController::class, 'footerSubMenuUpdate'])->name('footer.sub.menu.update');
            Route::get('sub/menu/delete/{id}', [LandingPageController::class, 'footerSubMenuDelete'])->name('footer.sub.menu.delete');

            //Header
            Route::get('header-setting', [LandingPageController::class, 'headerSetting'])->name('landing.header.index');

            Route::get('headersub/menu/create', [LandingPageController::class, 'headerSubMenuCreate'])->name('header.sub.menu.create');
            Route::post('headersub/menu/store', [LandingPageController::class, 'headerSubMenuStore'])->name('header.sub.menu.store');
            Route::get('headersub/menu/edit/{id}', [LandingPageController::class, 'headerSubMenuEdit'])->name('header.sub.menu.edit');
            Route::post('headersub/menu/update/{id}', [LandingPageController::class, 'headerSubMenuUpdate'])->name('header.sub.menu.update');
            Route::get('headersub/menu/delete/{id}', [LandingPageController::class, 'headerSubMenuDelete'])->name('header.sub.menu.delete');

            Route::get('start-view-setting', [LandingPageController::class, 'startViewSetting'])->name('landing.start.view.index');
            Route::post('start-view-setting/store', [LandingPageController::class, 'startViewSettingStore'])->name('landing.start.view.store');

            Route::get('faq-setting', [LandingPageController::class, 'faqSetting'])->name('landing.faq.index');
            Route::post('faq-setting/store', [LandingPageController::class, 'faqSettingStore'])->name('landing.faq.store');

            Route::get('contactus-setting', [LandingPageController::class, 'contactusSetting'])->name('landing.contactus.index');
            Route::post('contactus-setting/store', [LandingPageController::class, 'contactusSettingStore'])->name('landing.contactus.store');

            Route::get('login-setting', [LandingPageController::class, 'loginSetting'])->name('landing.login.index');
            Route::post('login-setting/store', [LandingPageController::class, 'loginSettingStore'])->name('landing.login.store');

            Route::get('recaptcha-setting', [LandingPageController::class, 'recaptchaSetting'])->name('landing.recaptcha.index');
            Route::post('recaptcha-setting/store', [LandingPageController::class, 'recaptchaSettingStore'])->name('landing.recaptcha.store');

            Route::get('blog-setting', [LandingPageController::class, 'blogSetting'])->name('landing.blog.index');
            Route::post('blog-setting/store', [LandingPageController::class, 'blogSettingStore'])->name('landing.blog.store');

            Route::get('testimonial-setting', [LandingPageController::class, 'testimonialSetting'])->name('landing.testimonial.index');
            Route::post('testimonial-setting/store', [LandingPageController::class, 'testimonialSettingStore'])->name('landing.testimonial.store');

            Route::get('page-background-setting', [LandingPageController::class, 'pageBackground'])->name('landing.page.background.index');
            Route::post('page-background-setting/store', [LandingPageController::class, 'pageBackgroundStore'])->name('landing.page.background.tore');
        });

        //document
        Route::resource('document', DocumentGenratorController::class);
        Route::get('document/design/{id}', [DocumentGenratorController::class, 'design'])->name('document.design');
        Route::post('document/design-menu/{id}', [DocumentGenratorController::class, 'documentDesignMenu'])->name('document.design.menu');

        //status drag-drop
        Route::post('document/designmenu', [DocumentGenratorController::class, 'updateDesign'])->name('updatedesign.document');
        Route::get('document-status/{id}', [DocumentGenratorController::class, 'documentStatus'])->name('document.status');

        // menu
        Route::get('docmenu/create/{docmenuId}', [DocumentMenuController::class, 'create'])->name('docmenu.create');
        Route::post('docmenu/store', [DocumentMenuController::class, 'store'])->name('docmenu.store');
        Route::delete('document/menu/{id}', [DocumentMenuController::class, 'destroy'])->name('document.designdelete');

        // submenu
        Route::get('docsubmenu/create/{id}/{docMenuId}', [DocumentMenuController::class, 'subMenuCreate'])->name('docsubmenu.create');
        Route::post('docsubmenu/store', [DocumentMenuController::class, 'subMenuStore'])->name('docsubmenu.store');
        Route::get('document/submenu/{id}', [DocumentMenuController::class, 'subMenuDestroy'])->name('document.submenu.designdelete');


        //flutterwave
        Route::post('flutterwave/payment', [FlutterwaveController::class, 'flutterwavePayment'])->name('pay.flutterwave.payment');
        Route::get('flutterwave/transaction/callback/{transactionId}/{couponId}/{plansId}', [FlutterwaveController::class, 'FlutterwaveCallback']);

        // Mollie
        Route::post('plan-pay-with-mollie', [MolliePaymentController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie');
        Route::get('plan/mollie/{plan}', [MolliePaymentController::class, 'getPaymentStatus'])->name('plan.mollie');
    });


    // cookie
    Route::get('cookie/consent', [SettingsController::class, 'CookieConsent'])->name('cookie.consent');

    // cache
    Route::any('config-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');
        return redirect()->back()->with('success', __('Cache Clear Successfully'));
    })->name('config.cache');

    // public document
    Route::get('document/public/{slug}', [DocumentGenratorController::class, 'documentPublic'])->name('document.public')->middleware(['xss', 'Upload']);
    Route::get('documents/{slug}/{changeLog?}', [DocumentGenratorController::class, 'documentPublicMenu'])->name('document.menu.menu')->middleware(['xss', 'Upload']);
    Route::get('document/{slug}/{slugmenu}', [DocumentGenratorController::class, 'documentPublicSubmenu'])->name('document.sub.menu')->middleware(['xss', 'Upload']);
    Route::get('/', [LandingController::class, 'landingPage'])->name('landingpage')->middleware('Upload');
    Route::get('changeLang/{lang?}', [LandingController::class, 'changeLang'])->name('change.lang');
});
