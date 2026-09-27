<?php
$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($base === '/' || $base === '.') {
    $base = '';
}
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
use App\Middleware\AuthMiddleware;
// Remove query string
$uri = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Remove base path (case-insensitive for local environments)
if ($base !== '' && stripos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}

$uri = trim($uri, '/');
if (stripos($uri, 'index.php') === 0) {
    $uri = trim(substr($uri, strlen('index.php')), '/');
}
switch ($uri) {
    // ── SEO Crawling Directives & Sitemaps ──
    case 'sitemap.xml':
    case 'sitemap':
        require_once __DIR__ . '/Controllers/SeoController.php';
        (new App\Controllers\SeoController())->sitemap();
        break;

    case 'robots.txt':
    case 'robots':
        require_once __DIR__ . '/Controllers/SeoController.php';
        (new App\Controllers\SeoController())->robots();
        break;

    case '':
        require_once __DIR__ . '/Controllers/HomeController.php';
        (new App\Controllers\HomeController())->index();
        break;

    case 'home':
        require_once __DIR__ . '/Controllers/HomeController.php';
        (new App\Controllers\HomeController())->index();
        break;
    case 'why-choose-us':
        require_once __DIR__ . '/Controllers/HomeController.php';
        (new App\Controllers\HomeController())->whychooseus();
        break;
    case 'list_your_farm':
        require_once __DIR__ . '/Controllers/HomeController.php';
        (new App\Controllers\HomeController())->owner_register();
        break;

    case 'about':
        require_once __DIR__ . '/Controllers/StaticController.php';
        (new App\Controllers\StaticController())->about();
        break;

    case 'contact':
        require_once __DIR__ . '/Controllers/StaticController.php';
        (new App\Controllers\StaticController())->contact();
        break;

    case 'contact-submit':
        require_once __DIR__ . '/Controllers/StaticController.php';
        (new App\Controllers\StaticController())->submitContact();
        break;
    case 'farmhouses':
        require_once __DIR__ . '/Controllers/FarmhousesController.php';
        (new App\Controllers\FarmhousesController())->farmhouses();
        break;

    case 'farmhouse_details':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->farmhouse_details();
        break;

    case 'farmhouse/request_booking':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->request_booking();
        break;

    case 'log_inquiry':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->logInquiry();
        break;

    case 'api/calculate-price':
    case 'booking/calculate-price':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->calculatePrice();
        break;

    case 'api/validate-coupon':
    case 'booking/validate-coupon':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->validateCoupon();
        break;

    case 'booking/voucher':
    case 'booking/invoice':
    case 'booking/download-voucher':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->bookingVoucher();
        break;

    case 'api/submit-review':
    case 'farmhouse/submit-review':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->submitReview();
        break;

    case 'api/room-availability':
    case 'booking/room-availability':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->getRoomAvailability();
        break;

    case 'api/room-calendar-matrix':
    case 'booking/room-calendar-matrix':
        require_once __DIR__ . '/Controllers/FarmdetailsController.php';
        (new App\Controllers\FarmdetailsController())->getRoomCalendarMatrix();
        break;

    case 'wishlist':
    case 'my-wishlist':
    case 'user/wishlist':
        require_once __DIR__ . '/Controllers/WishlistController.php';
        (new App\Controllers\WishlistController)->wishlist();
        break;

    case 'toggle_wishlist':
        require_once __DIR__ . '/Controllers/WishlistController.php';
        (new App\Controllers\WishlistController())->toggle();
        break;

    // Notification Center Routes (F56, F57, F58)
    case 'notifications':
        require_once __DIR__ . '/Controllers/NotificationController.php';
        (new App\Controllers\NotificationController())->index();
        break;

    case 'api/notifications':
        require_once __DIR__ . '/Controllers/NotificationController.php';
        (new App\Controllers\NotificationController())->apiGet();
        break;

    case 'api/notifications/mark-read':
        require_once __DIR__ . '/Controllers/NotificationController.php';
        (new App\Controllers\NotificationController())->apiMarkRead();
        break;

    case 'api/notifications/mark-all-read':
        require_once __DIR__ . '/Controllers/NotificationController.php';
        (new App\Controllers\NotificationController())->apiMarkAllRead();
        break;

    case 'login':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->index();
        break;
    case 'login-process':
    case 'login-submit':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->login();
        break;
    case 'logout':
    case 'user/logout':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->logout();
        break;

    // Google Social Auth Routes
    case 'auth/google':
    case 'login/google':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->googleAuth();
        break;

    case 'auth/google/callback':
    case 'auth/google-one-tap':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->googleCallback();
        break;

    // Owner Auth Routes
    case 'owner/login':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->ownerLogin();
        break;
    case 'owner/login-submit':
    case 'owner/login-process':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->login();
        break;
    case 'owner/logout':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->logout();
        break;
        
        
     case 'forgot-password':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->showForgotForm();
        break;
    
    case 'owner/forgot-password':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->showOwnerForgotForm();
        break;
    
    case 'forgot-password-process':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->sendResetLink();
        break;

    case 'forgot-password-verify':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->showDualVerifyForm();
        break;

    case 'forgot-password-verify-process':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->processDualVerification();
        break;

    case 'forgot-password-resend':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->resendDualOtp();
        break;
    
    case 'reset-password':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->showResetForm();
        break;
    
    case 'reset-password-process':
        require_once __DIR__ . '/Controllers/ForgotPasswordController.php';
        (new App\Controllers\ForgotPasswordController)->resetPassword();
        break;

    // case 'register':
    // require_once __DIR__ . '/Controllers/RegisterController.php';
    // (new App\Controllers\RegisterController)->register();
    // break;

    case 'terms_conditions':
    case 'terms':
        require_once __DIR__ . '/Controllers/TermsconditionsController.php';
        (new App\Controllers\TermsconditionsController)->terms_conditions();
        break;

    case 'privacy':
        require_once __DIR__ . '/Controllers/PrivacyController.php';
        (new App\Controllers\PrivacyController)->privacy();
        break;

    case 'cancellation-policy':
    case 'cancellation':
        require_once __DIR__ . '/Controllers/StaticController.php';
        (new App\Controllers\StaticController)->cancellation();
        break;

    case 'cookie-policy':
    case 'cookies':
        require_once __DIR__ . '/Controllers/StaticController.php';
        (new App\Controllers\StaticController)->cookiePolicy();
        break;

    // case 'wishlist': // This loads the page showing all saved farmhouses
    //     require_once __DIR__ . '/Controllers/WishlistController.php';
    //     (new App\Controllers\WishlistController())->wishlist();
    //     break;

    // case 'toggle_wishlist': 
    //     require_once __DIR__ . '/Controllers/WishlistController.php';
    //     (new App\Controllers\WishlistController())->toggle();
    //     break;
    
    
    
      //=====================User Dashboard============================== 

    // User Registration Routes
    case 'register':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->register(); // GET (View) and POST (Process)
        break;

    case 'verify-registration-otp':
        require_once __DIR__ . '/Controllers/AuthController.php';
        $authCtrl = new App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authCtrl->verifyRegistrationOtp();
        } else {
            $authCtrl->verifyRegistrationOtpView();
        }
        break;

    case 'resend-registration-otp':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->resendRegistrationOtp();
        break;

    case 'owner/register':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->ownerRegisterView();
        break;
    
    case 'owner/register-submit':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->ownerRegister();
        break;

    // User Dashboard
    case 'dashboard':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/DashboardController.php';
        (new App\Controllers\User\DashboardController)->dashboard();
        break;


    case 'user/farmhouses':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/FarmhouseController.php';
        (new App\Controllers\User\FarmhouseController)->index();
        break;

    // Booking Page
    case 'user/booking':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/BookingController.php';
        (new App\Controllers\User\BookingController)->index();
        break;

    // My Bookings History
    case 'user/my-bookings':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/MyBookingsController.php';
        $myBkCtrl = new App\Controllers\User\MyBookingsController;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $myBkCtrl->cancel();
        } else {
            $myBkCtrl->index();
        }
        break;

    case 'user/my-bookings/cancel':
        AuthMiddleware::userOnly();
        require_once __DIR__ . '/Controllers/User/MyBookingsController.php';
        (new App\Controllers\User\MyBookingsController)->cancel();
        break;

    // User Profile
    case 'user/profile':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/ProfileController.php';
        (new App\Controllers\User\ProfileController)->index();
        break;
    case 'profile/update':
        AuthMiddleware::userOnly(); // Security Middleware
        require_once __DIR__ . '/Controllers/User/ProfileController.php';
        (new App\Controllers\User\ProfileController)->update();
        break;

    case 'user/profile/verify-phone-otp':
        AuthMiddleware::userOnly();
        require_once __DIR__ . '/Controllers/User/ProfileController.php';
        (new App\Controllers\User\ProfileController)->verifyPhoneOtp();
        break;

    case 'user/profile/resend-phone-otp':
        AuthMiddleware::userOnly();
        require_once __DIR__ . '/Controllers/User/ProfileController.php';
        (new App\Controllers\User\ProfileController)->resendPhoneOtp();
        break;

    case 'user/profile/cancel-phone-change':
        AuthMiddleware::userOnly();
        require_once __DIR__ . '/Controllers/User/ProfileController.php';
        (new App\Controllers\User\ProfileController)->cancelPhoneChange();
        break;

    // Register (Guest Only)
    case 'user/register':
        AuthMiddleware::guestOnly(); // Prevent logged-in users
        require_once __DIR__ . '/Controllers/User/AuthController.php';
        (new App\Controllers\User\AuthController)->register();
        break;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


    // ==================admin Dashboard=================================

    case 'admin/login':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->adminLogin();
        break;

    case 'admin/login-submit':
    case 'admin/login-process':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->login();
        break;

    case 'admin/logout':
        require_once __DIR__ . '/Controllers/AuthController.php';
        (new App\Controllers\AuthController)->logout();
        break;



    case 'admin/dashboard':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/DashboardController.php';
        (new App\Controllers\Admin\DashboardController)->dashboard();
        break;



    // ============================================================
    //  ROUTER SNIPPET — replace your 3 old cases with these 3
    //  All three routes now use the single FarmhouseController.
    // ============================================================

    case 'admin/addfarm':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/FarmhouseController.php';
        (new App\Controllers\Admin\FarmhouseController)->add_farm();
        break;

    case 'admin/editfarm':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/FarmhouseController.php';
        (new App\Controllers\Admin\FarmhouseController)->edit_farmhouse();
        break;

    case 'admin/managefarmhouses':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/FarmhouseController.php';
        (new App\Controllers\Admin\FarmhouseController)->manage_farmhouses();
        break;

    case 'admin/managefarmhouses/details':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/FarmhouseController.php';
        (new App\Controllers\Admin\FarmhouseController)->propertyDetails();
        break;

    case 'admin/managefarmhouses/export':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/FarmhouseController.php';
        (new App\Controllers\Admin\FarmhouseController)->exportProperties();
        break;


    // =========================================================
    // PROPERTY TYPES ROUTING (DYNAMIC ARCHITECTURE SYSTEM)
    // =========================================================
    case 'admin/property-types':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyTypeController.php';
        (new App\Controllers\Admin\PropertyTypeController)->index();
        break;

    case 'admin/property-types/save':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyTypeController.php';
        (new App\Controllers\Admin\PropertyTypeController)->save();
        break;

    case 'admin/property-types/toggle':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyTypeController.php';
        (new App\Controllers\Admin\PropertyTypeController)->toggle();
        break;

    case 'admin/property-types/delete':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyTypeController.php';
        (new App\Controllers\Admin\PropertyTypeController)->delete();
        break;

    // =========================================================
    // COUPON & DISCOUNT ENGINE ROUTING (M5 PRICING ENGINE)
    // =========================================================
    case 'admin/coupons':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/CouponController.php';
        (new App\Controllers\Admin\CouponController)->index();
        break;

    case 'admin/coupons/save':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/CouponController.php';
        (new App\Controllers\Admin\CouponController)->save();
        break;

    case 'admin/coupons/toggle':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/CouponController.php';
        (new App\Controllers\Admin\CouponController)->toggle();
        break;

    case 'admin/coupons/delete':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/CouponController.php';
        (new App\Controllers\Admin\CouponController)->delete();
        break;

    // =========================================================
    // AMENITIES ROUTING
    // =========================================================

    case 'admin/manageamenities':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/AmenityController.php';
        (new App\Controllers\Admin\AmenityController)->manage();
        break;

    case 'admin/addamenity':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/AmenityController.php';
        (new App\Controllers\Admin\AmenityController)->add();
        break;

    case 'admin/editamenity':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/AmenityController.php';
        (new App\Controllers\Admin\AmenityController)->edit();
        break;

    // =========================================================
    // PROPERTY RULES (RULE PRESETS) ROUTING
    // =========================================================

    case 'admin/managerules':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyRuleController.php';
        (new App\Controllers\Admin\PropertyRuleController)->manage();
        break;

    case 'admin/addrule':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyRuleController.php';
        (new App\Controllers\Admin\PropertyRuleController)->add();
        break;

    case 'admin/editrule':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PropertyRuleController.php';
        (new App\Controllers\Admin\PropertyRuleController)->edit();
        break;


    case 'admin/inquiries':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/InquiriesController.php';
        (new App\Controllers\Admin\InquiriesController)->inquiries();
        break;

    case 'admin/profile':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ProfileControllers.php';
        (new App\Controllers\Admin\ProfileController)->profile();
        break;

    case 'admin/settings':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/SettingsController.php';
        (new App\Controllers\Admin\SettingsController)->settings();
        break;

    case 'admin/booking-requests':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->bookingRequests();
        break;

    case 'admin/booking-requests/details':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->bookingDetails();
        break;

    case 'admin/booking-requests/export':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->exportBookings();
        break;

    case 'admin/bookings':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->bookingRequests();
        break;

    case 'admin/blocked-dates':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->blockedDates();
        break;

    case 'admin/blocked-dates/lock':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->lockDate();
        break;

    case 'admin/blocked-dates/unlock':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/BookingRequestController.php';
        (new App\Controllers\Admin\BookingRequestController)->unlockDate();
        break;


    // Admin Payment Verification Routes
    case 'admin/payments':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PaymentController.php';
        (new App\Controllers\Admin\PaymentController)->payments();
        break;

    case 'admin/payments/verify':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PaymentController.php';
        (new App\Controllers\Admin\PaymentController)->verify();
        break;

    case 'admin/payments/reject':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/PaymentController.php';
        (new App\Controllers\Admin\PaymentController)->reject();
        break;

    // Admin Review Moderation Routes
    case 'admin/reviews':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ReviewController.php';
        (new App\Controllers\Admin\ReviewController)->reviews();
        break;

    case 'admin/reviews/approve':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ReviewController.php';
        (new App\Controllers\Admin\ReviewController)->approve();
        break;

    case 'admin/reviews/reject':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ReviewController.php';
        (new App\Controllers\Admin\ReviewController)->reject();
        break;

    case 'admin/reviews/delete':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ReviewController.php';
        (new App\Controllers\Admin\ReviewController)->delete();
        break;

    case 'admin/owners':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/OwnerController.php';
        (new App\Controllers\Admin\OwnerController)->owners();
        break;

    case 'admin/owners/details':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/OwnerController.php';
        (new App\Controllers\Admin\OwnerController)->ownerDetails();
        break;

    case 'admin/owners/export':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/OwnerController.php';
        (new App\Controllers\Admin\OwnerController)->exportOwners();
        break;
    case 'admin/users':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/UserController.php';
        (new App\Controllers\Admin\UserController)->users();
        break;

    case 'admin/users/online-status':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/UserController.php';
        (new App\Controllers\Admin\UserController)->onlineStatus();
        break;

    case 'admin/users/details':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/UserController.php';
        (new App\Controllers\Admin\UserController)->userDetails();
        break;

    case 'admin/users/export':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/UserController.php';
        (new App\Controllers\Admin\UserController)->exportUsers();
        break;

    case 'admin/contact-inquiries':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ContactInquiriesController.php';
        (new App\Controllers\Admin\ContactInquiriesController)->index();
        break;

    case 'admin/contact-inquiries/view':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ContactInquiriesController.php';
        (new App\Controllers\Admin\ContactInquiriesController)->view();
        break;

    case 'admin/contact-inquiries/delete':
        AuthMiddleware::adminOnly();
        require_once __DIR__ . '/Controllers/Admin/ContactInquiriesController.php';
        (new App\Controllers\Admin\ContactInquiriesController)->delete();
        break;



































    //=====================Owner Dashboard============================== 
    //=====================Owner Dashboard============================== 
    //=====================Owner Dashboard============================== 
    //=====================Owner Dashboard============================== 
// ====================================================================
//  OWNER ROUTES  —  paste these into your router's switch/case block
// ====================================================================

// ── Dashboard ────────────────────────────────────────────────────────
case 'owner/dashboard':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerDashboardController.php';
    (new App\Controllers\Owner\OwnerDashboardController)->index();
    break;

// ── Bookings: list & manage ──────────────────────────────────────────
case 'owner/bookings':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBookingController.php';
    (new App\Controllers\Owner\OwnerBookingController)->index();
    break;

case 'owner/bookings/details':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBookingController.php';
    (new App\Controllers\Owner\OwnerBookingController)->details();
    break;

case 'owner/bookings/update-status':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBookingController.php';
    (new App\Controllers\Owner\OwnerBookingController)->updateStatus();
    break;

// ── Farmhouses: list ─────────────────────────────────────────────────
case 'owner/farmhouses':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerFarmhouseController.php';
    (new App\Controllers\Owner\OwnerFarmhouseController)->index();
    break;

// ── Farmhouses: show detail ──────────────────────────────────────────
case 'owner/farmhouses/show':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerFarmhouseController.php';
    (new App\Controllers\Owner\OwnerFarmhouseController)->show();
    break;

// ── Farmhouses: add (GET = form, POST = persist) ─────────────────────
case 'owner/farmhouses/add':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerFarmhouseController.php';
    $ctrl = new App\Controllers\Owner\OwnerFarmhouseController;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ctrl->add();
    } else {
        $ctrl->addView();
    }
    break;

// ── Farmhouses: edit (GET = form, POST = persist) ────────────────────
case 'owner/farmhouses/edit':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerFarmhouseController.php';
    (new App\Controllers\Owner\OwnerFarmhouseController)->edit();
    break;

// ── Farmhouses: toggle status ────────────────────────────────────────
case 'owner/farmhouses/toggle-status':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerFarmhouseController.php';
    (new App\Controllers\Owner\OwnerFarmhouseController)->toggleStatus();
    break;

// ── Profile ──────────────────────────────────────────────────────────
case 'owner/profile':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerProfileController.php';
    (new App\Controllers\Owner\OwnerProfileController)->index();
    break;

// ── Profile: update info ─────────────────────────────────────────────
case 'owner/profile/update':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerProfileController.php';
    (new App\Controllers\Owner\OwnerProfileController)->update();
    break;

// ── Profile: change password ─────────────────────────────────────────
case 'owner/profile/change-password':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerProfileController.php';
    (new App\Controllers\Owner\OwnerProfileController)->changePassword();
    break;

// ── Date Locks (Blocked Dates) ───────────────────────────────────────
case 'owner/blocked-dates':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBlockedDatesController.php';
    (new App\Controllers\Owner\OwnerBlockedDatesController)->index();
    break;

case 'owner/blocked-dates/lock':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBlockedDatesController.php';
    (new App\Controllers\Owner\OwnerBlockedDatesController)->lock();
    break;

case 'owner/blocked-dates/unlock':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerBlockedDatesController.php';
    (new App\Controllers\Owner\OwnerBlockedDatesController)->unlock();
    break;
// ── Owner KYC Verification ───────────────────────────────────────────
case 'owner/kyc':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerKycController.php';
    $ctrl = new App\Controllers\Owner\OwnerKycController;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ctrl->upload();
    } else {
        $ctrl->index();
    }
    break;

case 'owner/kyc/delete':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerKycController.php';
    (new App\Controllers\Owner\OwnerKycController)->delete();
    break;

// ── Owner Offline Bookings ───────────────────────────────────────────
case 'owner/offline-bookings':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerOfflineBookingController.php';
    (new App\Controllers\Owner\OwnerOfflineBookingController)->index();
    break;

case 'owner/offline-bookings/create':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerOfflineBookingController.php';
    (new App\Controllers\Owner\OwnerOfflineBookingController)->create();
    break;

case 'owner/offline-bookings/cancel':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerOfflineBookingController.php';
    (new App\Controllers\Owner\OwnerOfflineBookingController)->cancel();
    break;

// ── Owner Earnings & Payouts ─────────────────────────────────────────
case 'owner/earnings':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerEarningsController.php';
    (new App\Controllers\Owner\OwnerEarningsController)->index();
    break;

case 'owner/earnings/request-payout':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerEarningsController.php';
    (new App\Controllers\Owner\OwnerEarningsController)->requestPayout();
    break;

// ── Owner Inquiries & Leads ──────────────────────────────────────────
case 'owner/inquiries':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerInquiriesController.php';
    (new App\Controllers\Owner\OwnerInquiriesController)->index();
    break;

case 'owner/inquiries/update-status':
    AuthMiddleware::ownerOnly();
    require_once __DIR__ . '/Controllers/Owner/OwnerInquiriesController.php';
    (new App\Controllers\Owner\OwnerInquiriesController)->updateStatus();
    break;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
?>