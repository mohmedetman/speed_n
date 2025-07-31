
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaytmController;

use App\Models\Order;
use App\Models\User;
use App\Jobs\CheckOrderStatus;
use App\Http\Controllers\LiqPayController;
use App\Http\Controllers\PaymobController;
use App\Http\Controllers\PaytabsController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\RazorPayController;
use App\Http\Controllers\SenangPayController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\BkashPaymentController;
use App\Http\Controllers\FlutterwaveV3Controller;
use App\Http\Controllers\PaypalPaymentController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\SslCommerzPaymentController;
use Google\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
use Illuminate\Support\Facades\DB;

try {
    // Start transaction explicitly
    DB::beginTransaction();
    
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Delete in proper order (child tables first)
    DB::table('user_notifications')->truncate();
    DB::table('user_infos')->truncate();
    DB::table('users')->truncate();
    
    // Reset auto-increment counters
    DB::statement('ALTER TABLE user_notifications AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE user_infos AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    // Commit transaction
    DB::commit();
    
    return "All user data deleted successfully";
} catch (\Exception $e) {
    // Rollback if error occurs
    DB::rollBack();
    // Ensure foreign key checks are re-enabled even on error
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    return "Error: " . $e->getMessage();
}|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('test_email', function() {

    $exists = User::where('email',request()->email)->exists();  
    return response()->json(['exists' => $exists]);
});

Route::get('test_phone', function() {
    $exists = User::where('phone','+'. request()->phone)->exists();  
    return response()->json(['exists' => $exists]);
});


Route::get('reset_password',function(){
 $exists = User::where('phone','+'. request()->phone)->first();
//dd()
 $exists->password = bcrypt(request()->password);
$exists->save();
return response()->json(['sucess']);



});

Route::get('/test', function (Request $request) {
//return Order::orderBy('id')->limit(10)->get();

$lastOrder = Order::orderBy('id', 'desc')->first();
//return  $lastOrder ;
$order = new Order();
        $order->user_id = $request->user ? $request->user->id : null;

        $order->order_amount = $request->order_amount ?? 120 ;
        $order->payment_method = $request->payment_method;
        $order->order_status = 'pending';
        $order->order_type = $request->order_type;
        $order->store_id = $request->store_id;
        $order->delivery_charge =1; // From your calculations
        $order->original_delivery_charge = 1;
        $order->delivery_address = json_encode("ewewfewfewf");
        $order->zone_id = $zone->id ?? 1; // From your zone logic
        $order->module_id = $request->header('moduleId')??1;
        $order->is_guest = $request->is_guest ?? 0;
   //     $order->created_at = now();
     //   $order->updated_at = now();
   //     $order->save();
//     return $order ;
       CheckOrderStatus::dispatch($order->id)->delay(now()->addMinutes(15));


   
//return \App\Models\Order::where('id',100264)->get() ;
    
});

Route::get('/test2', function (Request $request) {
//return Order::orderBy('id')->limit(10)->get();

$lastOrder = Order::orderBy('id', 'desc')->first();
return  $lastOrder ;
});
Route::get('/test/not', function () {
//    try {
        // Path to your Firebase service account key
        $serviceAccountKeyFilePath = storage_path('app/speed-65012-firebase-adminsdk-sir9v-78d7e9b59b.json');
        // Initialize Google Client
        $client = new Client();
        $client->setAuthConfig($serviceAccountKeyFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
          $deviceTokens = DeliveryMan::where('active',1)->pluck('fcm_token')->toArray();
        $url = 'https://fcm.googleapis.com/v1/projects/speed-65012/messages:send';
        $guzzleClient = new GuzzleClient();
        foreach ($deviceTokens as $token) {
            $data = [
                "message" => [
                    "token" => $token, // Single token for each request
                    "notification" => [
                        "title" => 'new order',
                        "body" => 'ahmed',
                    ],
                ],
            ];
            try {
                // Send the request to Firebase Cloud Messaging
                $response = $guzzleClient->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data,
                ]);

                // Log success response
                \Log::info('FCM Response: ' . $response->getBody()->getContents());
            } catch (\Exception $innerException) {
                \Log::error('FCM Error for token ' . $token . ': ' . $innerException->getMessage());
            }
        }

//        return redirect()->back()->with('success', 'Notifications sent successfully.');

//    } catch (\Exception $e) {
//        // Log the main exception for debugging purposes
//        \Log::error('Notification Error: ' . $e->getMessage());
//
//        // Redirect back with error message
//        return redirect()->back()->with('error', 'Failed to send notifications. Please check logs.');
//    }
});

Route::post('/subscribeToTopic', [FirebaseController::class, 'subscribeToTopic']);
// Route::get('/', 'HomeController@index')->name('home');
Route::get('/', 'LoginController@login')->name('login');
Route::get('lang/{locale}', 'HomeController@lang')->name('lang');
Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('contact-us', 'HomeController@contact_us')->name('contact-us');
Route::post('send-message', 'HomeController@send_message')->name('send-message');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');
Route::get('cancelation', 'HomeController@cancelation')->name('cancelation');
Route::get('refund', 'HomeController@refund_policy')->name('refund');
Route::get('shipping-policy', 'HomeController@shipping_policy')->name('shipping-policy');
Route::post('newsletter/subscribe', 'NewsletterController@newsLetterSubscribe')->name('newsletter.subscribe');
Route::get('subscription-invoice/{id}', 'HomeController@subscription_invoice')->name('subscription_invoice');
Route::get('order-invoice/{id}', 'HomeController@order_invoice')->name('order_invoice');

Route::get('login/{tab?}', 'LoginController@login')->name('login');
Route::post('external-login-from-drivemond', 'LoginController@externalLoginFromDrivemond');
Route::post('login_submit', 'LoginController@submit')->name('login_post')->middleware('actch');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::get('/reload-captcha', 'LoginController@reloadCaptcha')->name('reload-captcha');
Route::get('/reset-password', 'LoginController@reset_password_request')->name('reset-password');
Route::post('/vendor-reset-password', 'LoginController@vendor_reset_password_request')->name('vendor-reset-password');
Route::get('/password-reset', 'LoginController@reset_password')->name('change-password');
Route::post('verify-otp', 'LoginController@verify_token')->name('verify-otp');
Route::post('reset-password-submit', 'LoginController@reset_password_submit')->name('reset-password-submit');
Route::get('otp-resent', 'LoginController@otp_resent')->name('otp_resent');

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');
Route::get('payment-cancel', 'PaymentController@cancel')->name('payment-cancel');

$is_published = 0;
try {
$full_data = include('Modules/Gateways/Addon/info.php');
$is_published = $full_data['is_published'] == 1 ? 1 : 0;
} catch (\Exception $exception) {}

if (!$is_published) {
    Route::group(['prefix' => 'payment'], function () {

        //SSLCOMMERZ
        Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
            Route::get('pay', [SslCommerzPaymentController::class, 'index'])->name('pay');
            Route::post('success', [SslCommerzPaymentController::class, 'success'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('failed', [SslCommerzPaymentController::class, 'failed'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('canceled', [SslCommerzPaymentController::class, 'canceled'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //STRIPE
        Route::group(['prefix' => 'stripe', 'as' => 'stripe.'], function () {
            Route::get('pay', [StripePaymentController::class, 'index'])->name('pay');
            Route::get('token', [StripePaymentController::class, 'payment_process_3d'])->name('token');
            Route::get('success', [StripePaymentController::class, 'success'])->name('success');
            Route::get('canceled', [StripePaymentController::class, 'canceled'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //RAZOR-PAY
        Route::group(['prefix' => 'razor-pay', 'as' => 'razor-pay.'], function () {
            Route::get('pay', [RazorPayController::class, 'index']);
            Route::post('payment', [RazorPayController::class, 'payment'])->name('payment')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //PAYPAL
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('pay', [PaypalPaymentController::class, 'payment']);
            Route::any('success', [PaypalPaymentController::class, 'success'])->name('success')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;
            Route::any('cancel', [PaypalPaymentController::class, 'cancel'])->name('cancel')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;
        });

        //SENANG-PAY
        Route::group(['prefix' => 'senang-pay', 'as' => 'senang-pay.'], function () {
            Route::get('pay', [SenangPayController::class, 'index']);
            Route::any('callback', [SenangPayController::class, 'return_senang_pay']);
        });

        //PAYTM
        Route::group(['prefix' => 'paytm', 'as' => 'paytm.'], function () {
            Route::get('pay', [PaytmController::class, 'payment']);
            Route::any('response', [PaytmController::class, 'callback'])->name('response')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //FLUTTERWAVE
        Route::group(['prefix' => 'flutterwave-v3', 'as' => 'flutterwave-v3.'], function () {
            Route::get('pay', [FlutterwaveV3Controller::class, 'initialize'])->name('pay');
            Route::get('callback', [FlutterwaveV3Controller::class, 'callback'])->name('callback');
        });

        //PAYSTACK
        Route::group(['prefix' => 'paystack', 'as' => 'paystack.'], function () {
            Route::get('pay', [PaystackController::class, 'index'])->name('pay');
            Route::post('payment', [PaystackController::class, 'redirectToGateway'])->name('payment');
            Route::get('callback', [PaystackController::class, 'handleGatewayCallback'])->name('callback');
        });

        //BKASH

        Route::group(['prefix' => 'bkash', 'as' => 'bkash.'], function () {
            // Payment Routes for bKash
            Route::get('make-payment', [BkashPaymentController::class, 'make_tokenize_payment'])->name('make-payment');
            Route::any('callback', [BkashPaymentController::class, 'callback'])->name('callback');

            // Refund Routes for bKash
            // Route::get('refund', 'BkashRefundController@index')->name('bkash-refund');
            // Route::post('refund', 'BkashRefundController@refund')->name('bkash-refund');
        });

        //Liqpay
        Route::group(['prefix' => 'liqpay', 'as' => 'liqpay.'], function () {
            Route::get('payment', [LiqPayController::class, 'payment'])->name('payment');
            Route::any('callback', [LiqPayController::class, 'callback'])->name('callback');
        });

        //MERCADOPAGO

        Route::group(['prefix' => 'mercadopago', 'as' => 'mercadopago.'], function () {
            Route::get('pay', [MercadoPagoController::class, 'index'])->name('index');
            Route::any('make-payment', [MercadoPagoController::class, 'make_payment'])->name('make_payment')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::get('success', [MercadoPagoController::class, 'success'])->name('success');
            Route::get('failed', [MercadoPagoController::class, 'failed'])->name('failed');
        });

        //PAYMOB
        Route::group(['prefix' => 'paymob', 'as' => 'paymob.'], function () {
            Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
            Route::any('callback', [PaymobController::class, 'callback'])->name('callback');
        });

        //PAYTABS
        Route::group(['prefix' => 'paytabs', 'as' => 'paytabs.'], function () {
            Route::any('pay', [PaytabsController::class, 'payment'])->name('pay');
            Route::any('callback', [PaytabsController::class, 'callback'])->name('callback');
            Route::any('response', [PaytabsController::class, 'response'])->name('response');
        });
    });
}


// Route::get('/test', function () {
//     dd('Hello tester');
// });

Route::get('module-test', function () {
});

//Restaurant Registration
Route::group(['prefix' => 'store', 'as' => 'restaurant.'], function () {
    Route::get('apply', 'VendorController@create')->name('create');
    Route::post('apply', 'VendorController@store')->name('store');
    Route::get('get-all-modules', 'VendorController@get_all_modules')->name('get-all-modules');

    Route::get('back', 'VendorController@back')->name('back');
    Route::post('business-plan', 'VendorController@business_plan')->name('business_plan');
    Route::post('payment', 'VendorController@payment')->name('payment');
    Route::get('final-step', 'VendorController@final_step')->name('final_step');
});

//Deliveryman Registration
Route::group(['prefix' => 'deliveryman', 'as' => 'deliveryman.'], function () {
    Route::get('apply', 'DeliveryManController@create')->name('create');
    Route::post('apply', 'DeliveryManController@store')->name('store');
});
