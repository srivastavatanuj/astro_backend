<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\API\Astrologer\AdsVideoController;
use App\Http\Controllers\API\Astrologer\AdvertisementController;
use App\Http\Controllers\API\Astrologer\AssistantChatController;
use App\Http\Controllers\API\Astrologer\AstroHostController;
use App\Http\Controllers\API\Astrologer\AstrologerAssistantController;
use App\Http\Controllers\API\Astrologer\AstrologerAvailabilityController;
use App\Http\Controllers\API\Astrologer\AstrologerCategoryController;
use App\Http\Controllers\API\Astrologer\AstrologerController;
// use App\Http\Controllers\API\Astrologer\AstrologerProductController;
use App\Http\Controllers\API\Astrologer\AstrologerStoryController;
use App\Http\Controllers\API\Astrologer\BlogController;
use App\Http\Controllers\API\Astrologer\LiveAstroController;
use App\Http\Controllers\API\Astrologer\PermissionController;
use App\Http\Controllers\API\Astrologer\SkillController;
use App\Http\Controllers\API\Astrologer\WithDrawController;
use App\Http\Controllers\API\User\AppReviewController;
use App\Http\Controllers\API\User\AstromallProductController;
use App\Http\Controllers\API\User\BannerController;
use App\Http\Controllers\API\User\CallRequestController;
use App\Http\Controllers\API\User\ChatRequestController;
use App\Http\Controllers\API\User\CommissionController;
use App\Http\Controllers\API\User\CommissionTypeController;
use App\Http\Controllers\API\User\CouponController;
use App\Http\Controllers\API\User\CustomerHomeController;
use App\Http\Controllers\API\User\DailyHoroscopeController;
use App\Http\Controllers\API\User\FollowerController;
use App\Http\Controllers\API\User\GiftController;
use App\Http\Controllers\API\User\HelpSupportController;
use App\Http\Controllers\API\User\HelpSupportQuestionController;
use App\Http\Controllers\API\User\HoroController;
use App\Http\Controllers\API\User\KundaliController;
use App\Http\Controllers\API\User\KundaliMatchingController;
use App\Http\Controllers\API\User\LanguageController;
use App\Http\Controllers\API\User\LiveUserController;
use App\Http\Controllers\API\User\MaritalStatusController;
use App\Http\Controllers\API\User\NotificationController;
use App\Http\Controllers\API\User\OrderAddressController;
use App\Http\Controllers\API\User\PaymentController;
use App\Http\Controllers\API\User\ProductCategoryController;
use App\Http\Controllers\API\User\ProductDetailController;
// use App\Http\Controllers\API\User\ReportController;
use App\Http\Controllers\API\User\ReportTypeController;
use App\Http\Controllers\API\User\SystemFlagController;
use App\Http\Controllers\API\User\TicketController;
use App\Http\Controllers\API\User\TokenGeneratorController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\User\UserNotificationController;
use App\Http\Controllers\API\User\UserOrderController;
use App\Http\Controllers\API\User\UserReportController;
use App\Http\Controllers\API\User\UserReviewController;
use App\Http\Controllers\API\User\WaitListController;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\Route;


$session = new Session();
$token = $session->get('token');
header('Authorization:Bearer '.$token);
// header('Content-Type:application/json');
header('Accept:application/json');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['middleware' => 'api'], function () {
    Route::post('login', [UserController::class, 'loginUser']);
    Route::post('user/add', [UserController::class, 'addUser']);
    Route::post('getProfile', [UserController::class, 'getProfile']);
    Route::post('refresh', [UserController::class, 'refreshToken']);
    Route::post('user/update/{id}', [UserController::class, 'updateUser'])->name('user.update');
    Route::post('password/reset', [UserController::class, 'forgotPassword']);
    Route::post('userStatus/update/{id}', [UserController::class, 'activeUser']);
    Route::post('user/delete', [UserController::class, 'deleteUser'])->name('api.deleteUser');
    Route::post('user/updateProfile', [UserController::class, 'updateUserProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('validateSession', [AstrologerController::class, 'validateSession']);
    Route::post('validateSessionForAstrologer', [AstrologerController::class, 'validateSessionForAstrologer']);
});

// Get Otl Response
Route::post('getOtlResponse', [AstrologerController::class, 'getOtlResponse']);


// New For Web
Route::post('getUserdetails', [AstrologerController::class, 'getUserdetails']);
Route::post('getAstrologerdetails', [AstrologerController::class, 'getAstrologerdetails']);



Route::post('getUser', [UserController::class, 'getUsers']);
Route::post('getUserById', [AstrologerController::class, 'getUserById']);
Route::post('getUserProfile', [AstrologerController::class, 'getUserProfile']);
//customer
Route::post('loginAppUser', [UserController::class, 'loginAppUser']);
//Device details
Route::post('deviceDetails', [UserController::class, 'addDeviceDetails']);

//------------------------------------------------ADMIN API--------------------------------------------------//
//Skill
Route::post('getSkill', [SkillController::class, 'getAppSkills']);
Route::post('activeSkill', [SkillController::class, 'activeSkills']);

//Gift
Route::post('getGift', [GiftController::class, 'getGifts']);
Route::post('activeGift', [GiftController::class, 'activeGifts']);
Route::post('sendGift', [GiftController::class, 'sendGifts'])->name('api.sendGifts');

//Astrologer category
Route::post('getAstrologerCategory', [AstrologerCategoryController::class, 'getAstrologerCategory']);
Route::post('activeAstrologerCategory', [AstrologerCategoryController::class, 'activeAstrologerCategory']);

//Hororscope sign
Route::post('getHororscopeSign', [HoroController::class, 'getHororscopeSign']);
Route::post('activeHororscopeSign', [HoroController::class, 'activeHororscopeSigns']);

//Product category
Route::post('getproductCategory', [ProductCategoryController::class, 'getProductCategory']);
Route::post('getTopProductCategory', [ProductCategoryController::class, 'gettopthreeproductcategory']);
//Coupon code
Route::post('getCouponcode', [CouponController::class, 'getCouponcode']);

//Banner
Route::post('getBanner', [BannerController::class, 'getBanner']);
Route::post('getBannerType', [BannerController::class, 'getBannerType']);
//Notification
Route::post('notification/add', [NotificationController::class, 'addNotification']);
Route::post('notification/update/{id}', [NotificationController::class, 'updateNotification']);
Route::post('getNotification', [NotificationController::class, 'getNotification']);
Route::post('notificationStatus/update/{id}', [NotificationController::class, 'activeNotification']);
Route::post('notification/delete', [NotificationController::class, 'deleteNotification']);
Route::post('notification/send', [NotificationController::class, 'sendNotification']);
Route::post('getActiveUser', [NotificationController::class, 'getUser']);
//User notification
Route::post('userNotification/add', [UserNotificationController::class, 'addUserNotification']);
Route::post('userNotification/update/{id}', [UserNotificationController::class, 'updateUserNotification']);
Route::post('getUserNotification', [UserNotificationController::class, 'getUserNotification'])->name('api.getUserNotification');
Route::post('userNotificationStatus/update/{id}', [UserNotificationController::class, 'activeUserNotification']);
Route::post('userNotification/deleteUserNotification', [NotificationController::class, 'deleteUserNotification']);
Route::post('userNotification/deleteAllNotification', [NotificationController::class, 'deleteAllUserNotification'])->name('api.deleteAllUserNotification');
//Permission
Route::post('permission/add', [PermissionController::class, 'addPermission']);
Route::post('permission/update/{id}', [PermissionController::class, 'updatePermission']);
Route::post('getPermission', [PermissionController::class, 'getPermission']);
Route::post('permissionStatus/update/{id}', [PermissionController::class, 'activePermission']);

//Commission type pending
Route::post('getCommissionType', [CommissionTypeController::class, 'getCommissionType']);

//Commission  pending
Route::post('getCommission', [CommissionController::class, 'getCommission']);

//Help and support
Route::post('getHelpSupport', [HelpSupportController::class, 'getHelpSupport']);

//Help and support questions
Route::post('getHelpSupportQuestion', [HelpSupportQuestionController::class, 'getHelpSupportQuestion']);

Route::post('getHelpSupportSubSubCategory', [HelpSupportQuestionController::class, 'getHelpSupportSubSubCategory']);

//Astrologer
Route::post('astrologer/add', [AstrologerController::class, 'addAstrologer']);
Route::post('astrologer/update', [AstrologerController::class, 'updateAstrologer']);
Route::post('getAstrologer', [AstrologerController::class, 'getAstrologer']);
Route::post('getCounsellor', [AstrologerController::class, 'getCounsellor']);
Route::post('astrologer/delete', [AstrologerController::class, 'deleteAstrologer']);
Route::post('astrologerVerify/update/{id}', [AstrologerController::class, 'verifyAstrologer']);
Route::post('getMasterAstrologer', [AstrologerController::class, 'masterAstrologer']);
Route::post('addAstrologerAvailability', [AstrologerAvailabilityController::class, 'addAstrologerAvailability']);
Route::post('updateAstrologerAvailability', [AstrologerAvailabilityController::class, 'updateAstrologerAvailability']);
Route::post('getAstrologerAvailability', [AstrologerAvailabilityController::class, 'getAstrologerAvailability']);
Route::post('checkContactNoExist', [AstrologerController::class, 'checkContactNoExist']);
Route::post('checkContactNoExistForUser', [AstrologerController::class, 'checkContactNoExistForUser'])->name('api.checkContactAndSendOTP');
Route::post('getAdminAstrologer', [AstrologerController::class, 'getAstrologerForAdmin']);
Route::post('getAstrologerById', [AstrologerController::class, 'getAstrologerById']);
Route::post('searchAstro', [AstrologerController::class, 'searchAstro']);
Route::post('getAstrologerForCustomer', [AstrologerController::class, 'getAstrologerForCustomer']);
Route::post('reportBlockAstrologer', [AstrologerController::class, 'reportblockAstrologer']);
Route::post('unBlockAstrologer', [AstrologerController::class, 'unblockAstrologer']);
Route::post('getBlockAstrologer', [AstrologerController::class, 'getBlockAstrologer']);
Route::post('addStatus', [ChatRequestController::class, 'addChatStatus'])->name('api.addChatStatus');
Route::post('addCallStatus', [CallRequestController::class, 'addCallStatus'])->name('api.addCallStatus');
Route::post('getAstrologerByIdForAdmin', [AstrologerController::class, 'getAstrologerByIdForAdmin']);
//Blog
Route::post('getBlog', [BlogController::class, 'getBlog']);
Route::post('getAppBlog', [BlogController::class, 'getAppBlog']);
Route::post('activeBlogs', [BlogController::class, 'activeBlogs']);
Route::post('addBlogReader', [BlogController::class, 'addBlogReader']);
Route::post('getBlogById', [BlogController::class, 'blogShow']);
//Astromall product
Route::post('getAstromallProduct', [AstromallProductController::class, 'getAstromallProductForApp']);
Route::post('getAstromallProductById', [AstromallProductController::class, 'getAstromallProductById']);
Route::post('searchAstromallProductCategory', [AstromallProductController::class, 'searchInProductCategory']);
//Ads banner

Route::post('getAdsVideo', [AdsVideoController::class, 'getAdsVideo']);

Route::post('getAdvertisement', [AdvertisementController::class, 'getAdvertisement']);

//Ticket
Route::post('ticket/add', [TicketController::class, 'addTicket']);
Route::post('ticket/update/{id}', [TicketController::class, 'updateTicket']);
Route::post('getTicket', [TicketController::class, 'getTicket']);
Route::post('ticketStatus/update/{id}', [TicketController::class, 'activeTicket']);
Route::post('ticket/deleteAll', [TicketController::class, 'deleteAllTicket']);
Route::post('ticket/addReview', [TicketController::class, 'addTicketReview']);
Route::post('ticket/getReview', [TicketController::class, 'getTicketReview']);
Route::post('ticket/updateReview', [TicketController::class, 'updateTicketReview']);
Route::post('ticket/closeTicket', [TicketController::class, 'closeTicket']);
Route::post('ticket/delete', [TicketController::class, 'deleteTicket']);
Route::post('ticket/restart', [TicketController::class, 'restartTicket']);
Route::post('ticket/pause', [TicketController::class, 'pauseTicket']);
Route::post('checkOpenTicket', [TicketController::class, 'checkOpenTicket']);
Route::post('updateTicketStatus', [TicketController::class, 'updateTicketStatus']);
//Product details
Route::post('productDetails/add', [ProductDetailController::class, 'addProductDetails']);
Route::post('productDetails/update/{id}', [ProductDetailController::class, 'updateProductDetails']);
Route::post('getProductDetails', [ProductDetailController::class, 'getProductDetails']);
Route::post('productDetailsStatus/update/{id}', [ProductDetailController::class, 'activeProductDetails']);

//------------------------------------------------CUSTOMER API--------------------------------------------------//
// New
Route::post('kundali/addnew', [KundaliController::class, 'addKundaliNew'])->name('api.addKundaliNew');
Route::post('kundali/getKundaliReport', [KundaliMatchingController::class, 'getKundaliReport'])->name('api.getKundaliReport');
//Kundali
Route::post('kundali/add', [KundaliController::class, 'addKundali'])->name('api.addKundali');
Route::post('kundali/update/{id}', [KundaliController::class, 'updateKundali']);
Route::post('pdf/price', [KundaliController::class, 'getKundaliPrice']);
Route::post('getkundali', [KundaliController::class, 'getKundalis']);
Route::post('kundali/delete', [KundaliController::class, 'deleteKundali'])->name('api.deleteKundali');
Route::post('kundali/get/{id}', [KundaliController::class, 'getKundali']);
Route::post('kundali/show/{id}', [KundaliController::class, 'kundaliShow']);
Route::post('kundali/removeFromTrackPlanet', [KundaliController::class, 'removeFromTrackPlanet']);
Route::post('kundali/addForTrackPlanet', [KundaliController::class, 'addForTrackPlanet']);
Route::post('kundali/getForTrackPlanet', [KundaliController::class, 'getForTrackPlanet']);
Route::post('get/panchang', [KundaliController::class, 'getPanchang']);
//Order address
Route::post('orderAddress/add', [OrderAddressController::class, 'addOrderAddress'])->name('api.addOrderAddress');
Route::post('orderAddress/update/{id}', [OrderAddressController::class, 'updateOrderAddress']);
Route::post('getOrderAddress', [OrderAddressController::class, 'getOrderAddress']);

//Home screen
Route::post('getCustomerHome', [CustomerHomeController::class, 'getCustomerHome']);

//Blog

//Kundali matching
Route::post('KundaliMatching/add', [KundaliMatchingController::class, 'addKundaliMatching']);
Route::post('KundaliMatching/report', [KundaliMatchingController::class, 'getMatchReport']);
Route::post('getKundaliReportById', [KundaliController::class, 'getKundaliReportById'])->name('api.getKundaliReportById');

//Language
Route::post('getLanguage', [LanguageController::class, 'getLanguages']);

//review
Route::post('userReview/add', [UserReviewController::class, 'addUserReview'])->name('api.addUserReview');
Route::post('userReview/update/{id}', [UserReviewController::class, 'updateUserReview']);
Route::post('getUserReview', [UserReviewController::class, 'getUserReview']);
Route::post('userReview/delete/{id}', [UserReviewController::class, 'deleteUserReview']);
Route::post('appReview/get', [AppReviewController::class, 'getAppReview']);
Route::post('appReview/add', [AppReviewController::class, 'addAppReview']);
Route::post('getAstrologerUserReview', [UserReviewController::class, 'getAstrologerUserReview'])->name('api.getAstrologerUserReview');
Route::post('getUserHistoryReview', [UserReviewController::class, 'getUserHistoryReview']);
Route::post('userReview/reply', [UserReviewController::class, 'replyAstrologerReview']);
Route::post('blockUserReview', [UserReviewController::class, 'blockUserReview']);
Route::post('getBlockUserReview', [UserReviewController::class, 'getBlockReview']);
Route::post('getUserReviewForAdmin', [UserReviewController::class, 'getAstrologerUserReviewForAdmin']);

//Follower
Route::post('follower/add', [FollowerController::class, 'addFollowing'])->name('api.addFollowing');
Route::post('follower/update', [FollowerController::class, 'updateFollowing'])->name('api.updateFollowing');
Route::post('getFollower', [FollowerController::class, 'getFollowing']);
Route::post('getAstrologerFollower', [FollowerController::class, 'getFollower']);

//User report
Route::post('userReport/add', [UserReportController::class, 'addReport'])->name('api.addReport');
Route::post('getUserReport', [UserReportController::class, 'getUserReportRequest']);
Route::post('userReport/update/{id}', [UserReportController::class, 'updateUserReport']);
Route::post('getUserReportRequestById/{id?}', [UserReportController::class, 'getUserReportRequestById']);


//Marital status
Route::post('getMaritalStatus', [MaritalStatusController::class, 'getMaritalStatus']);

//Report type
Route::post('getReportType', [ReportTypeController::class, 'getReportTypes']);
//------------------------------------------------ASTROLOGER API--------------------------------------------------//

//Login
Route::post('loginAppAstrologer', [AstrologerController::class, 'loginAstrologer']);

//Astrologer assistant
Route::post('astrologerAssistant/add', [AstrologerAssistantController::class, 'addAstrologerAssistant']);
Route::post('astrologerAssistant/update', [AstrologerAssistantController::class, 'updateAstrologerAssistant']);
Route::post('getAstrologerAssistant', [AstrologerAssistantController::class, 'getAstrologerAssistant']);
Route::post('astrologerAssistant/delete', [AstrologerAssistantController::class, 'deleteAstrologerAssistant']);
Route::post('astrologerAssistant/block', [AstrologerAssistantController::class, 'blockAstrologerAssistant']);
Route::post('astrologerAssistant/unBlock', [AstrologerAssistantController::class, 'unblockAstrologerAssistant']);
//Astrologer available
Route::post('astrologerAvailable/add', [AstrologerAvailabilityController::class, 'addAstrologerAvailable']);

//Astrologer Product
// Route::post('astrologerProduct/get', [AstrologerProductController::class, 'getAstrologerForProduct']);

//chatRequest
Route::post('chatcallrequest/deleteauto', [ChatRequestController::class, 'rejectChatCallAutomatic']);
Route::post('chatRequest/add', [ChatRequestController::class, 'addChatRequest'])->name('api.addChatRequest');
Route::post('chatRequest/get', [ChatRequestController::class, 'getChatRequest']);
Route::post('chatRequest/reject', [ChatRequestController::class, 'rejectChatRequest'])->name('api.rejectChatRequest');
Route::post('chatRequest/removeFromWaitList', [ChatRequestController::class, 'removeFromWaitlist']);
Route::post('chatRequest/accept', [ChatRequestController::class, 'acceptChatRequest'])->name('api.acceptChatRequest');
Route::post('chatRequest/storeToken', [ChatRequestController::class, 'storeToken']);
Route::post('chatRequest/storeChatId', [ChatRequestController::class, 'insertChatRequest'])->name('api.insertChatRequest');
Route::post('chatRequest/endChat', [ChatRequestController::class, 'endChatRequest'])->name('api.endChatRequest');
Route::post('chatRequest/acceptChatRequest', [ChatRequestController::class, 'acceptChatRequestFromCustomer'])->name('api.acceptChatRequestFromCustomer');
Route::post('chatRequest/rejectChatRequest', [ChatRequestController::class, 'rejectChatRequestFromCustomer'])->name('api.rejectChatRequestFromCustomer');
Route::post('chatRequest/endLiveChat', [ChatRequestController::class, 'endLiveChatrequest']);
Route::post('chatRequest/addIntakeForm', [ChatRequestController::class, 'intakeForm'])->name('api.intakeForm');
Route::post('chatRequest/getIntakeForm', [ChatRequestController::class, 'getUserIntakForm'])->name('api.getUserIntakForm');
Route::post('checkChatSessionAvailable', [ChatRequestController::class, 'checkChatSessionTaken'])->name('api.checkChatSessionTaken');
Route::post('checkCallSessionAvailable', [ChatRequestController::class, 'checkCallSessionTaken'])->name('api.checkCallSessionTaken');
Route::post('checkFreeSessionAvailable', [ChatRequestController::class, 'checkFreeSessionAvailable']);
//callRequest
Route::post('callRequest/add', [CallRequestController::class, 'addCallRequest'])->name('api.addCallRequest');
Route::post('callRequest/get', [CallRequestController::class, 'getCallRequest']);
Route::post('callRequest/reject', [CallRequestController::class, 'rejectCallRequest'])->name('api.rejectCallRequest');
Route::post('callRequest/removeFromWaitList', [CallRequestController::class, 'removeFromWaitlist']);
Route::post('callRequest/accept', [CallRequestController::class, 'acceptCallRequest'])->name('api.acceptCallRequest');
Route::post('callRequest/storeToken', [CallRequestController::class, 'storeToken'])->name('api.storeToken');
Route::post('callRequest/end', [CallRequestController::class, 'endCall'])->name('api.endCall');
Route::post('callRequest/acceptCallRequest', [CallRequestController::class, 'acceptCallRequestFromCustomer'])->name('api.acceptCallRequestFromCustomer');
Route::post('callRequest/rejectCallRequest', [CallRequestController::class, 'rejectCallRequestFromCustomer'])->name('api.rejectCallRequestFromCustomer');
Route::post('storeCallRecording', [CallRequestController::class, 'storeCallRecording']);
Route::post('getCallById', [CallRequestController::class, 'getCallById']);
//userOrder
Route::post('userOrder/add', [UserOrderController::class, 'addUserOrder'])->name('api.addUserOrder');
Route::post('userOrder/cancel', [UserOrderController::class, 'cancelOrder'])->name('api.cancelOrder');
//withdrawlRequest
Route::post('withdrawlrequest/add', [WithDrawController::class, 'sendWithdrawRequest'])->name('api.sendWithdrawRequest');
Route::post('withdrawlrequest/get', [WithDrawController::class, 'getWithdrawRequest']);
Route::post('withdrawlrequest/update', [WithDrawController::class, 'updateWithdrawRequest'])->name('api.updateWithdrawRequest');
Route::post('withdrawlrequest/releaseAmount', [WithDrawController::class, 'releaseAmount']);
Route::post('addpayment', [PaymentController::class, 'addPayment'])->name('user.addpayment');
Route::post('getRechargeAmount', [PaymentController::class, 'getRechargeAmount']);

// withdraw method
Route::post('withdrawlmethod/get', [WithDrawController::class, 'getWithdrawMethod']);
//liveAstro
Route::post('liveAstrologer/add', [LiveAstroController::class, 'addLiveAstrologer'])->name('api.addLiveAstrologer');
Route::post('liveAstrologer/get', [LiveAstroController::class, 'getLiveAstrologer']);
Route::post('liveAstrologer/endSession', [LiveAstroController::class, 'endLiveSession'])->name('api.endLiveSession');
Route::post('liveAstrologer/livechattoken', [LiveAstroController::class, 'addLiveChatToken']);
Route::post('liveAstrologer/getToken', [LiveAstroController::class, 'getToken']);
Route::post('liveAstrologer/getUpcomingAstrologer', [LiveAstroController::class, 'getUpcomingAstrologer']);
Route::post('searchLiveAstro', [LiveAstroController::class, 'searchLiveAstrologer']);
Route::post('liveAstrologerWeb/add', [LiveAstroController::class, 'addLiveAstrologerWeb'])->name('api.addLiveAstrologerWeb');

//astrohost
Route::post('getAstrohost', [AstroHostController::class, 'getAstrohost']);
Route::post('addAstrohost', [AstroHostController::class, 'addAstrohost']);

//waitlist

Route::post('waitlist/add', [WaitListController::class, 'addWaitList']);
Route::post('waitlist/get', [WaitListController::class, 'getWaitList']);
Route::post('waitlist/delete', [WaitListController::class, 'deleteFromWaitList']);
Route::post('waitlist/updateStatus', [WaitListController::class, 'editWaitList']);

//systemflag
Route::post('getSystemFlag', [SystemFlagController::class, 'getSystemFlag']);
Route::post('getSubCategory', [SystemFlagController::class, 'getSubCategory']);
Route::post('getAppLanguage', [SystemFlagController::class, 'getAppLanguage']);
//report

// Route::post('getCallHistoryReport', [ReportController::class, 'getCallHistory']);
// Route::post('getChatHistoryReport', [ReportController::class, 'getChatHistory']);
// Route::post('partnerWiseEarning', [ReportController::class, 'partnerWiseEarning']);
// Route::post('astrologerEarning', [ReportController::class, 'getAstrologerEarning']);
// Route::post('orderReport', [ReportController::class, 'orderRequest']);
//liveUser
Route::post('addLiveUser', [LiveUserController::class, 'addLiveUser']);
Route::post('getLiveUser', [LiveUserController::class, 'getLiveUser'])->name('api.getLiveUser');
Route::post('deleteLiveUser', [LiveUserController::class, 'deleteLiveUser']);

//userreprot
Route::post('userreport/add', [UserReportController::class, 'addUserReportFile'])->name('api.addUserReportFile');
Route::post('savetoken', [ChatRequestController::class, 'saveToken']);

//assistantChat
Route::post('getAssistantChatId', [AssistantChatController::class, 'getAssistantChat']);
Route::post('getAssistantChatHistory', [AssistantChatController::class, 'getAssistantChatHistory']);
Route::post('getCustomerPaidSession', [AssistantChatController::class, 'customerPaidSession']);
Route::post('getAssistantChatRequest', [AssistantChatController::class, 'getAssistantChatRequest']);
Route::post('deleteAssistantChat', [AssistantChatController::class, 'deleteAssistantChat']);
//dailyHoroscope
Route::post('getDailyHoroscope', [DailyHoroscopeController::class, 'getDailyHoroscope']);
Route::post('getDailyHoroscopeInsightForAdmin', [DailyHoroscopeController::class, 'getDailyHoroscopeInsightForAdmin']);
Route::post('getHoroscope', [DailyHoroscopeController::class, 'getHoroscope']);
// Route::post('generateToken', [TokenGeneratorController::class, 'generateToken']); // old one
Route::post('generateToken', [TokenGeneratorController::class, 'generateRtmToken']);
Route::post('generateRtcToken', [TokenGeneratorController::class, 'generateRtcToken'])->name('api.generateRtcToken');
Route::post('addHoroscopeFeedback', [DailyHoroscopeController::class, 'addHoroscopeFeedback']);
//cDemo

// pages
Route::post('privacy-policy', [DashboardController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::post('tnc', [DashboardController::class, 'termscond'])->name('termscond');


// Astrologer Stories
Route::post('addStory', [AstrologerStoryController::class, 'addStory'])->name('api.addStory');
Route::post('getStory', [AstrologerStoryController::class, 'getStory'])->name('api.getStory');
// view Count
Route::post('clickStory', [AstrologerStoryController::class, 'clickStory'])->name('clickStory');
Route::post('getAstrologerStory', [AstrologerStoryController::class, 'getAstrologerStory'])->name('getAstrologerStory');




