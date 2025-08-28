<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\DefaultProfile;
use App\Models\AdminModel\DegreeOrDiploma;
use App\Models\AdminModel\FulltimeJob;
use App\Models\AdminModel\HighestQualification;
use App\Models\AdminModel\Language;
use App\Models\AdminModel\MainSourceOfBusiness;
use App\Models\AdminModel\TravelCountry;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerAvailability;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\AstrologerModel\Skill;
use App\Models\UserModel\User;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\UserModel\UserRole;
use App\Models\UserModel\UserWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Request as Req;

class AstrologerController extends Controller
{

    // Get Response with otl token
    public function getOtlResponse(Request $request)
    {
        $client_id = DB::table('systemflag')->where('name', 'otplessClientId')->select('value')->first();
        $secret_key = DB::table('systemflag')->where('name', 'otplessSecretKey')->select('value')->first();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://auth.otpless.app/auth/userInfo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'token=' . $request->token . '&client_id=' . $client_id->value . '&client_secret=' . $secret_key->value . '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }


    //Add Astrologer
    public function addAstrologer(Request $req)
    {
        try {
            DB::beginTransaction();
            $data = $req->only(
                'name',
                'email',
                'contactNo',
                'gender',
                'birthDate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'profileImage',
                'charge',
                'experienceInYears',
                'dailyContribution',
                'isWorkingOnAnotherPlatform',
                'whyOnBoard',
                'interviewSuitableTime',
                'mainSourceOfBusiness',
                'highestQualification',
                'degree',
                'college',
                'learnAstrology',
                'astrologerCategoryId',
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink',
                'isAnyBodyRefer',
                'minimumEarning',
                'maximumEarning',
                'loginBio',
                'NoofforeignCountriesTravel',
                'currentlyworkingfulltimejob',
                'goodQuality',
                'biggestChallenge',
                'whatwillDo',
                'isVerified',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerCategoryId' => 'required',
                'name' => 'required|string',
                'email' => 'required|unique:users,email',
                'contactNo' => 'required|max:10|unique:users,contactNo',
                'gender' => 'required',
                'birthDate' => 'required',
                'dailyContribution' => 'required',
                'languageKnown' => 'required',
                'primarySkill' => 'required',
                'allSkill' => 'required',
                'languageKnown' => 'required',
                'charge' => 'required',
                'experienceInYears' => 'required',
                'interviewSuitableTime' => 'required',
                'mainSourceOfBusiness' => 'required',
                'minimumEarning' => 'required',
                'maximumEarning' => 'required',
                'NoofforeignCountriesTravel' => 'required',
                'currentlyworkingfulltimejob' => 'required',
                'goodQuality' => 'required',
                'biggestChallenge' => 'required',
                'whatwillDo' => 'required',
                'charge' => 'required',
                'whyOnBoard' => 'required',
                'highestQualification' => 'required',
            ]);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $user = User::create([
                'name' => $req->name,
                'contactNo' => $req->contactNo,
                'email' => $req->email,
                'birthDate' => $req->birthDate,
                'gender' => $req->gender,
                'location' => $req->currentCity,
                'countryCode' => $req->countryCode,
            ]);
            //Create a new astrologer
            $astrologer = Astrologer::create([
                'name' => $req->name,
                'userId' => $user->id,
                'email' => $req->email,
                'contactNo' => $req->contactNo,
                'gender' => $req->gender,
                'birthDate' => $req->birthDate,
                'primarySkill' => implode(',', array_column($req->primarySkill, 'id')),
                'allSkill' => implode(',', array_column($req->allSkill, 'id')),
                'languageKnown' => implode(',', array_column($req->languageKnown, 'id')),
                'charge' => $req->charge,
                'experienceInYears' => $req->experienceInYears,
                'dailyContribution' => $req->dailyContribution,
                'hearAboutAstroguru' => $req->hearAboutAstroguru,
                'isWorkingOnAnotherPlatform' => $req->isWorkingOnAnotherPlatform,
                'whyOnBoard' => $req->whyOnBoard,
                'interviewSuitableTime' => $req->interviewSuitableTime,
                'currentCity' => $req->currentCity,
                'mainSourceOfBusiness' => $req->mainSourceOfBusiness,
                'highestQualification' => $req->highestQualification,
                'degree' => $req->degree,
                'college' => $req->college,
                'learnAstrology' => $req->learnAstrology,
                'astrologerCategoryId' => implode(',', array_column($req->astrologerCategoryId, 'id')),
                'instaProfileLink' => $req->instaProfileLink,
                'linkedInProfileLink' => $req->linkedInProfileLink,
                'facebookProfileLink' => $req->facebookProfileLink,
                'websiteProfileLink' => $req->websiteProfileLink,
                'youtubeChannelLink' => $req->youtubeChannelLink,
                'isAnyBodyRefer' => $req->isAnyBodyRefer,
                'minimumEarning' => $req->minimumEarning,
                'maximumEarning' => $req->maximumEarning,
                'loginBio' => $req->loginBio,
                'NoofforeignCountriesTravel' => $req->NoofforeignCountriesTravel,
                'currentlyworkingfulltimejob' => $req->currentlyworkingfulltimejob,
                'goodQuality' => $req->goodQuality,
                'biggestChallenge' => $req->biggestChallenge,
                'whatwillDo' => $req->whatwillDo,
                'isVerified' => false,
                'highestQualification' => $req->highestQualification,
                'charge' => $req->charge,
                'hearAboutAstroguru' => $req->hearAboutAstroguru,
                'whyOnBoard' => $req->whyOnBoard,
                'currentCity' => $req->currentCity,
                'country' => $req->country,
                'videoCallRate' => $req->videoCallRate,
                'reportRate' => $req->reportRate ? $req->reportRate : 0,
                'nameofplateform' => $req->nameofplateform,
                'monthlyEarning' => $req->monthlyEarning,
                'referedPerson' => $req->referedPerson,
                'videoCallRate' => $req->videoCallRate,
            ]);
            if ($req->profileImage) {
                $time = Carbon::now()->timestamp;
                $destinationpath = 'public/storage/images/';
                $imageName = 'astrologer_' . $user->id . $time;
                $path = $destinationpath . $imageName . '.png';
                file_put_contents($path, base64_decode($req->profileImage));
            } else {
                $path = null;
            }
            $user->profile = $path;
            $user->update();
            $astrologer->profileImage = $path;
            $astrologer->update();
            UserRole::create([
                'userId' => $user->id,
                'roleId' => 2,
            ]);
            if ($req->userDeviceDetails) {
                UserDeviceDetail::create([
                    'userId' => $user->id,
                    'appId' => $req->appId,
                    'deviceId' => $req->deviceId,
                    'fcmToken' => $req->fcmToken,
                    'deviceLocation' => $req->deviceLocation,
                    'deviceManufacturer' => $req->deviceManufacturer,
                    'deviceModel' => $req->deviceModel,
                    'appVersion' => $req->appVersion,
                ]);
            }
            if ($req->astrologerAvailability) {
                foreach ($req->astrologerAvailability as $astrologeravailable) {
                    foreach ($astrologeravailable['time'] as $availability) {
                        AstrologerAvailability::create([
                            'astrologerId' => $astrologer['id'],
                            'day' => $astrologeravailable['day'],
                            'fromTime' => $availability['fromTime'],
                            'toTime' => $availability['toTime'],
                            'createdBy' => $astrologer['id'],
                            'modifiedBy' => $astrologer['id'],
                        ]);
                    }
                }
            }
            $astrologer->astrologerAvailability = $req->astrologerAvailability;
            $astrologer->allSkill = array_map('intval', explode(',', $astrologer->allSkill));
            $astrologer->primarySkill = array_map('intval', explode(',', $astrologer->primarySkill));
            $astrologer->languageKnown = array_map('intval', explode(',', $astrologer->languageKnown));
            $astrologer->astrologerCategoryId = array_map('intval', explode(',', $astrologer->astrologerCategoryId));
            $allSkill = DB::table('skills')
                ->whereIn('id', $astrologer->allSkill)
                ->select('name', 'id')
                ->get();
            $primarySkill = DB::table('skills')
                ->whereIn('id', $astrologer->primarySkill)
                ->select('name', 'id')
                ->get();
            $languageKnown = DB::table('languages')
                ->whereIn('id', $astrologer->languageKnown)
                ->select('languageName', 'id')
                ->get();
            $category = DB::table('astrologer_categories')
                ->whereIn('id', $astrologer->astrologerCategoryId)
                ->select('name', 'id')
                ->get();
            $astrologer->allSkill = $allSkill;
            $astrologer->primarySkill = $primarySkill;
            $astrologer->languageKnown = $languageKnown;
            $astrologer->astrologerCategoryId = $category;
            DB::commit();
            return response()->json([
                'message' => 'Astrologer add sucessfully',
                'recordList' => $astrologer,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Login astrologer
    public function loginAstrologer(Request $req)
    {
        try {
            $credentials = $req->only('contactNo');
            $email_credentials = $req->only('email');
            // dd($credentials);

            if ($req->contactNo) {
                $astrologer = DB::table('astrologers')
                    ->join('user_roles', 'astrologers.userId', '=', 'user_roles.userId')
                    ->where('contactNo', '=', $req->contactNo)
                    ->where('user_roles.roleId', '=', $req->roleId = 2)
                    ->where('astrologers.isDelete', '=', false)
                    ->select('astrologers.*')
                    ->get();
            } elseif ($req->email) {
                $astrologer = DB::table('astrologers')
                    ->join('user_roles', 'astrologers.userId', '=', 'user_roles.userId')
                    ->where('email', '=', $req->email)
                    ->where('user_roles.roleId', '=', $req->roleId = 2)
                    ->where('astrologers.isDelete', '=', false)
                    ->select('astrologers.*')
                    ->get();
            }


            if ($astrologer && count($astrologer) > 0) {
                if (!$astrologer[0]->isVerified) {
                    return response()->json([
                        'message' => 'Your Account is not verified from admin',
                        'status' => 400,
                    ], 400);
                } else {
                    if ($req->contactNo && !$token = Auth::guard('api')->attempt($credentials)) {
                        return response()->json([
                            'error' => false,
                            'message' => 'Contact number is incorrect',
                            'status' => 401,
                        ], 401);
                    } elseif ($req->email && !$token = Auth::guard('api')->attempt($email_credentials)) {
                        return response()->json([
                            'error' => false,
                            'message' => 'Email is Incorrect',
                            'status' => 401,
                        ], 401);
                    } else {
                        if ($req->userDeviceDetails) {

                            if ($req->contactNo) {
                                $userDeviceDetail = DB::table('user_device_details')
                                    ->join('users', 'users.id', '=', 'user_device_details.userId')
                                    ->where('users.contactNo', '=', $req->contactNo)
                                    ->select('user_device_details.*')
                                    ->get();
                            }
                            if ($req->email) {
                                $userDeviceDetail = DB::table('user_device_details')
                                    ->join('users', 'users.id', '=', 'user_device_details.userId')
                                    ->where('users.email', '=', $req->email)
                                    ->select('user_device_details.*')
                                    ->get();
                                // dd($userDeviceDetail);
                            }

                            if ($userDeviceDetail && count($userDeviceDetail) == 0) {
                                $userDeviceDetail = UserDeviceDetail::create([
                                    'userId' => $astrologer[0]->userId,
                                    'appId' => $req->userDeviceDetails['appId'],
                                    'deviceId' => $req->userDeviceDetails['deviceId'],
                                    'fcmToken' => $req->userDeviceDetails['fcmToken'],
                                    'deviceLocation' => $req->userDeviceDetails['deviceLocation'],
                                    'deviceManufacturer' => $req->userDeviceDetails['deviceManufacturer'],
                                    'deviceModel' => $req->userDeviceDetails['deviceModel'],
                                    'appVersion' => $req->userDeviceDetails['appVersion'],
                                ]);
                            } else {
                                $userDeviceDetail = UserDeviceDetail::find($userDeviceDetail[0]->id);
                                if ($userDeviceDetail) {
                                    $userDeviceDetail->appId = $req->userDeviceDetails['appId'];
                                    $userDeviceDetail->deviceId = $req->userDeviceDetails['deviceId'];
                                    $userDeviceDetail->fcmToken = $req->userDeviceDetails['fcmToken'];
                                    $userDeviceDetail->deviceLocation = $req->userDeviceDetails['deviceLocation'];
                                    $userDeviceDetail->deviceManufacturer =
                                        $req->userDeviceDetails['deviceManufacturer'];
                                    $userDeviceDetail->deviceModel = $req->userDeviceDetails['deviceModel'];
                                    $userDeviceDetail->appVersion = $req->userDeviceDetails['appVersion'];
                                    $userDeviceDetail->updated_at = Carbon::now()->timestamp;
                                    $userDeviceDetail->update();
                                }
                            }
                        }
                        if ($astrologer) {
                            $astrologer[0]->allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                            $astrologer[0]->primarySkill =
                                array_map('intval', explode(',', $astrologer[0]->primarySkill));
                            $astrologer[0]->languageKnown =
                                array_map('intval', explode(',', $astrologer[0]->languageKnown));
                            $astrologer[0]->astrologerCategoryId =
                                array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                            $allSkill = DB::table('skills')
                                ->whereIn('id', $astrologer[0]->allSkill)
                                ->select('name', 'id')
                                ->get();
                            $primarySkill = DB::table('skills')
                                ->whereIn('id', $astrologer[0]->primarySkill)
                                ->select('name', 'id')
                                ->get();
                            $languageKnown = DB::table('languages')
                                ->whereIn('id', $astrologer[0]->languageKnown)
                                ->select('languageName', 'id')
                                ->get();
                            $category = DB::table('astrologer_categories')
                                ->whereIn('id', $astrologer[0]->astrologerCategoryId)
                                ->select('name', 'id')
                                ->get();
                            $astrologer[0]->allSkill = $allSkill;
                            $astrologer[0]->primarySkill = $primarySkill;
                            $astrologer[0]->languageKnown = $languageKnown;
                            $astrologer[0]->astrologerCategoryId = $category;
                            $astrologerAvailability = DB::table('astrologer_availabilities')
                                ->where('astrologerId', '=', $astrologer[0]->id)
                                ->get();
                            $working = [];
                            if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                                $day = [];

                                $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                foreach ($day as $days) {
                                    $day = array(
                                        'day' => $days,
                                    );
                                    $currentday = $days;
                                    $result =
                                        array_filter(
                                            json_decode($astrologerAvailability),
                                            function ($event) use ($currentday) {
                                                return $event->day === $currentday;
                                            }
                                        );
                                    $ti = [];

                                    foreach ($result as $available) {
                                        $time = array(
                                            'fromTime' => $available->fromTime,
                                            'toTime' => $available->toTime,
                                        );
                                        array_push($ti, $time);
                                    }
                                    $weekDay = array(
                                        'day' => $days,
                                        'time' => $ti,
                                    );
                                    array_push($working, $weekDay);
                                }
                            }
                            $astrologer[0]->astrologerAvailability = $working;
                        }
                        return $this->respondWithTokenApp($token, $astrologer);
                    }
                }
            } else {
                if ($req->contactNo) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Contact No is Not Register',
                    ], 400);
                } elseif ($req->email) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Email is Not Registered',
                    ], 400);
                }
            }

            //token

            //Go to token generation

        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Generate token
    protected function respondWithTokenApp($token, $id)
    {
        try {
            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'status' => 200,
                'recordList' => $id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Get all the data of the astrologer
    public function getAstrologer(Request $req)
    {
        try {
            $astrologer = Astrologer::query();
            $astrologer->where('astrologers.isActive', '=', true)
                ->where('isVerified', '=', true)
                ->where('astrologers.isDelete', '=', false);
            if ($s = $req->input('s')) {
                $astrologer->whereRaw("name LIKE '%" . $s . "%'");
            }
            $astrologer = $astrologer->get();

            $isFreeAvailable = true;
            $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
            if ($isFreeChat->value == 1) {
                if ($req->userId) {
                    $isChatRequest = DB::table('chatrequest')->where('userId', $req->userId)->where('chatStatus', '=', 'Completed')->first();
                    $isCallRequest = DB::table('callrequest')->where('userId', $req->userId)->where('callStatus', '=', 'Completed')->first();
                    if ($isChatRequest || $isCallRequest) {
                        $isFreeAvailable = false;
                    } else {
                        $isFreeAvailable = true;
                    }
                }
            } else {
                $isFreeAvailable = false;
            }



            // Set isFreeAvailable property for each astrologer
            foreach ($astrologer as $astro) {
                $astro->isFreeAvailable = $isFreeAvailable;
            }
            $astro = [];
            if ($req->astrologerCategoryId) {
                $category = $req->astrologerCategoryId;
                for ($i = 0; $i < count($astrologer); $i++) {
                    $categoryAstrologer =
                        array_filter(
                            json_decode(json_encode(
                                array_map(
                                    'intval',
                                    explode(',', $astrologer[$i]->astrologerCategoryId)
                                )
                            )),
                            function ($event) use ($category) {
                                return $event === $category;
                            }
                        );
                    if ($categoryAstrologer && count($categoryAstrologer) > 0) {
                        array_push($astro, $astrologer[$i]);
                    }
                }
                $astrologer = $astro;
            }
            $isFilter = false;
            if ($req->filterData) {
                if (
                    array_key_exists('skills', $req->filterData) && $req->filterData['skills']
                    && count($req->filterData['skills']) > 0
                ) {
                    $isFilter = true;
                    $skillAstrologer = [];
                    for ($i = 0; $i < count($astrologer); $i++) {
                        $allSkill = array_map('intval', explode(',', $astrologer[$i]->allSkill));
                        foreach ($req->filterData['skills'] as $skill) {
                            $all =
                                array_filter(
                                    json_decode(json_encode(array_map(
                                        'intval',
                                        explode(',', $astrologer[$i]->allSkill)
                                    ))),
                                    function ($event) use ($skill) {
                                        return $event === $skill;
                                    }
                                );
                            if ($all && count($all) > 0) {
                                $ast = $astrologer[$i];
                                $allastro = array_filter($skillAstrologer, function ($event) use ($ast) {
                                    return $event->id === $ast->id;
                                });
                                if (!($allastro && count($allastro) > 0)) {
                                    array_push($skillAstrologer, $astrologer[$i]);
                                }
                            }
                        }
                    }
                    $astrologer = $skillAstrologer;
                }
                if (
                    array_key_exists('languageKnown', $req->filterData)
                    && $req->filterData['languageKnown'] && count($req->filterData['languageKnown']) > 0
                ) {
                    $isFilter = true;
                    $languageAstrologer = [];
                    for ($i = 0; $i < count($astrologer); $i++) {
                        $languages = array_map('intval', explode(',', $astrologer[$i]->languageKnown));
                        foreach ($req->filterData['languageKnown'] as $language) {
                            $all =
                                array_filter(
                                    json_decode(json_encode(array_map(
                                        'intval',
                                        explode(',', $astrologer[$i]->languageKnown)
                                    ))),
                                    function ($event) use ($language) {
                                        return $event === $language;
                                    }
                                );
                            if ($all && count($all) > 0) {
                                $ast = $astrologer[$i];
                                $allastro = array_filter($languageAstrologer, function ($event) use ($ast) {
                                    return $event->id === $ast->id;
                                });
                                if (!($allastro && count($allastro) > 0)) {
                                    array_push($languageAstrologer, $astrologer[$i]);
                                }
                            }
                        }
                    }
                    $astrologer = $languageAstrologer;
                }
                if (
                    array_key_exists('gender', $req->filterData)
                    && $req->filterData['gender'] && count($req->filterData['gender']) > 0
                ) {
                    $isFilter = true;
                    $genderAstrologer = [];
                    for ($j = 0; $j < count($req->filterData['gender']); $j++) {
                        for ($i = 0; $i < count($astrologer); $i++) {
                            if ($astrologer[$i]->gender == $req->filterData['gender'][$j]) {
                                array_push($genderAstrologer, $astrologer[$i]);
                            }
                        }
                    }
                    $astrologer = $genderAstrologer;
                }
                if (
                    array_key_exists('country', $req->filterData)
                    && $req->filterData['country'] && count($req->filterData['country']) > 0
                ) {
                    $isFilter = true;
                    $countryAstrologer = [];
                    for ($i = 0; $i < count($astrologer); $i++) {
                        if ($req->filterData['country'][0] == 'India') {
                            if ($astrologer[$i]->country == 'India') {
                                array_push($countryAstrologer, $astrologer[$i]);
                            }
                        } else {
                            if ($astrologer[$i]->country != 'India') {
                                array_push($countryAstrologer, $astrologer[$i]);
                            }
                        }
                    }
                    $astrologer = $countryAstrologer;
                }
            }

            if ($req->sortBy == 'experienceHighToLow') {
                $astrologers = collect($astrologer)->sortBy('experienceInYears')->reverse()->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'experienceLowToHigh') {
                $astrologers = collect($astrologer)->sortBy('experienceInYears')->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'ordersHighToLow') {
                $astrologers = collect($astrologer)->sortBy('totalOrder')->reverse()->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'ordersLowToHigh') {
                $astrologers = collect($astrologer)->sortBy('totalOrder')->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'priceHighToLow') {
                $astrologers = collect($astrologer)->sortBy('charge')->reverse()->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'priceLowToHigh') {
                $astrologers = collect($astrologer)->sortBy('charge')->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'reportPriceLowToHigh') {
                $astrologers = collect($astrologer)->sortBy('reportRate')->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            if ($req->sortBy == 'reportPriceHighToLow') {
                $astrologers = collect($astrologer)->sortBy('reportRate')->reverse()->toArray();
                $astrologer = [];
                foreach ($astrologers as $astro) {
                    array_push($astrologer, $astro);
                }
            }
            $astrologerCount = count($astrologer);

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                if ((!Req::exists('sortBy') || $req->sortBy == null)
                    && !$isFilter && !$req->astrologerCategoryId
                ) {
                    $astrologer = array_slice(json_decode($astrologer), $req->startIndex, $req->fetchRecord);
                } else {
                    $astrologer = array_slice($astrologer, $req->startIndex, $req->fetchRecord);
                }
            }
            $astr = [];

            if ($astrologer && count($astrologer) > 0) {
                if (!Req::exists('sortBy') || $req->sortBy == null) {
                    foreach ($astrologer as $astro) {
                        $review = DB::table('user_reviews')
                            ->where('astrologerId', '=', $astro->id)
                            ->get();

                        $astro->rating = 0;
                        if ($review && count($review) > 0) {
                            $avgRating = 0;
                            foreach ($review as $re) {
                                $avgRating += $re->rating;
                            }
                            $avgRating = $avgRating / count($review);
                            $astro->rating = $avgRating;
                        }



                        $astrologerCategory = array_map('intval', explode(',', $astro->astrologerCategoryId));
                        $allSkill = array_map('intval', explode(',', $astro->allSkill));
                        $primarySkill = array_map('intval', explode(',', $astro->primarySkill));
                        $languages = array_map('intval', explode(',', $astro->languageKnown));
                        $astro->reviews = $review ? count($review) : 0;

                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $primarySkill = DB::table('skills')
                            ->whereIn('id', $primarySkill)
                            ->select('name')
                            ->get();
                        $primary = $primarySkill->pluck('name')->all();
                        $astrologerCategory = DB::table('astrologer_categories')
                            ->whereIn('id', $astrologerCategory)
                            ->select('name')
                            ->get();
                        $astrologerCategories = $astrologerCategory->pluck('name')->all();
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnowns = $languageKnown->pluck('languageName')->all();

                        $astro->languageKnown = implode(",", $languageKnowns);
                        $astro->astrologerCategory = implode(",", $astrologerCategories);
                        $astro->allSkill = implode(",", $skill);
                        $astro->primarySkill = implode(",", $primary);
                        array_push($astr, $astro);
                    }
                } else {
                    foreach ($astrologer as $astro) {
                        $review = DB::table('user_reviews')
                            ->where('astrologerId', '=', $astro['id'])
                            ->get();
                        $astro['rating'] = 0;
                        if ($review && count($review) > 0) {
                            $avgRating = 0;
                            foreach ($review as $re) {
                                $avgRating += $re->rating;
                            }
                            $avgRating = $avgRating / count($review);
                            $astro['rating'] = $avgRating;
                        }
                        $astrologerCategory = array_map('intval', explode(',', $astro['astrologerCategoryId']));
                        $allSkill = array_map('intval', explode(',', $astro['allSkill']));
                        $primarySkill = array_map('intval', explode(',', $astro['primarySkill']));
                        $languages = array_map('intval', explode(',', $astro['languageKnown']));
                        $astro['reviews'] = $review ? count($review) : 0;

                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $primarySkill = DB::table('skills')
                            ->whereIn('id', $primarySkill)
                            ->select('name')
                            ->get();
                        $primary = $primarySkill->pluck('name')->all();
                        $astrologerCategory = DB::table('astrologer_categories')
                            ->whereIn('id', $astrologerCategory)
                            ->select('name')
                            ->get();
                        $astrologerCategories = $astrologerCategory->pluck('name')->all();
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnowns = $languageKnown->pluck('languageName')->all();

                        $astro['languageKnown'] = implode(",", $languageKnowns);
                        $astro['astrologerCategory'] = implode(",", $astrologerCategories);
                        $astro['allSkill'] = implode(",", $skill);
                        $astro['primarySkill'] = implode(",", $primary);
                        array_push($astr, $astro);
                    }
                }
            }
            if (Req::exists('sortBy') || $req->sortBy != null) {
                $astrologer = $astr;
            }
            if ($req->sortBy == 'rating') {
                $astrologer = collect($astrologer)->sortBy('rating')->reverse()->toArray();
            }
            error_log($isFreeAvailable);
            foreach ($astrologer as $astro) {
                if ($req->sortBy) {
                    $astro['isFreeAvailable'] = $isFreeAvailable;
                } else {
                    $astro->isFreeAvailable = $isFreeAvailable;
                }
            }
            return response()->json([
                'recordList' => $astrologer,
                'status' => 200,
                'totalCount' => $astrologerCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Update astrologer
    public function updateAstrologer(Request $req)
    {
        try {
            $data = $req->only(
                'id',
                'userId',
                'name',
                'email',
                'contactNo',
                'gender',
                'birthDate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'profileImage',
                'charge',
                'experienceInYears',
                'dailyContribution',
                'isWorkingOnAnotherPlatform',
                'whyOnBoard',
                'interviewSuitableTime',
                'mainSourceOfBusiness',
                'highestQualification',
                'degree',
                'college',
                'learnAstrology',
                'astrologerCategoryId',
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink',
                'isAnyBodyRefer',
                'minimumEarning',
                'maximumEarning',
                'loginBio',
                'NoofforeignCountriesTravel',
                'currentlyworkingfulltimejob',
                'goodQuality',
                'biggestChallenge',
                'whatwillDo',
                'isVerified',
            );
            $user = User::find($req->userId);
            $validator = Validator::make($data, [
                'id' => 'required',
                'userId' => 'required',
                'astrologerCategoryId' => 'required',
                'name' => 'required|string',
                'contactNo' => 'required|unique:users,contactNo,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gender' => 'required',
                'birthDate' => 'required',
                'dailyContribution' => 'required',
                'languageKnown' => 'required',
                'primarySkill' => 'required',
                'allSkill' => 'required',
                'interviewSuitableTime' => 'required',
                'mainSourceOfBusiness' => 'required',
                'minimumEarning' => 'required',
                'maximumEarning' => 'required',
                'NoofforeignCountriesTravel' => 'required',
                'currentlyworkingfulltimejob' => 'required',
                'goodQuality' => 'required',
                'biggestChallenge' => 'required',
                'whatwillDo' => 'required',
                'charge' => 'required',
                'whyOnBoard' => 'required',
                'highestQualification' => 'required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            if ($req->profileImage) {
                if (Str::contains($req->profileImage, 'storage')) {
                    $path = $req->profileImage;
                } else {
                    $time = Carbon::now()->timestamp;
                    $destinationpath = 'public/storage/images/';
                    $imageName = 'astrologer_' . $req->id . $time;
                    $path = $destinationpath . $imageName . '.png';
                    $isFile = explode('.', $path);
                    if (!(file_exists($path) && count($isFile) > 1)) {
                        file_put_contents($path, base64_decode($req->profileImage));
                    }
                }
            } else {
                $path = null;
            }


            if ($user) {
                $user->name = $req->name;
                $user->contactNo = $req->contactNo;
                $user->email = $req->email;
                $user->birthDate = $req->birthDate;
                $user->profile = $path;
                $user->gender = $req->gender;
                $user->location = $req->currentCity;
                $user->countryCode = $req->countryCode;
                $user->update();
            }
            $astrologer = Astrologer::find($req->id);
            if ($astrologer) {
                $astrologer->name = $req->name;
                $astrologer->userId = $req->userId;
                $astrologer->email = $req->email;
                $astrologer->contactNo = $req->contactNo;
                $astrologer->gender = $req->gender;
                $astrologer->birthDate = $req->birthDate;
                $astrologer->primarySkill = implode(',', array_column($req->primarySkill, 'id'));
                $astrologer->allSkill = implode(',', array_column($req->allSkill, 'id'));
                $astrologer->languageKnown = implode(',', array_column($req->languageKnown, 'id'));
                $astrologer->profileImage = $path;
                $astrologer->charge = $req->charge;
                $astrologer->experienceInYears = $req->experienceInYears;
                $astrologer->dailyContribution = $req->dailyContribution;
                $astrologer->hearAboutAstroguru = $req->hearAboutAstroguru;
                $astrologer->isWorkingOnAnotherPlatform = $req->isWorkingOnAnotherPlatform;
                $astrologer->whyOnBoard = $req->whyOnBoard;
                $astrologer->interviewSuitableTime = $req->interviewSuitableTime;
                $astrologer->currentCity = $req->currentCity;
                $astrologer->mainSourceOfBusiness = $req->mainSourceOfBusiness;
                $astrologer->highestQualification = $req->highestQualification;
                $astrologer->degree = $req->degree;
                $astrologer->college = $req->college;
                $astrologer->learnAstrology = $req->learnAstrology;
                $astrologer->astrologerCategoryId = implode(',', array_column($req->astrologerCategoryId, 'id'));
                $astrologer->instaProfileLink = $req->instaProfileLink;
                $astrologer->linkedInProfileLink = $req->linkedInProfileLink;
                $astrologer->facebookProfileLink = $req->facebookProfileLink;
                $astrologer->websiteProfileLink = $req->websiteProfileLink;
                $astrologer->youtubeChannelLink = $req->youtubeChannelLink;
                $astrologer->isAnyBodyRefer = $req->isAnyBodyRefer;
                $astrologer->minimumEarning = $req->minimumEarning;
                $astrologer->maximumEarning = $req->maximumEarning;
                $astrologer->loginBio = $req->loginBio;
                $astrologer->NoofforeignCountriesTravel = $req->NoofforeignCountriesTravel;
                $astrologer->currentlyworkingfulltimejob = $req->currentlyworkingfulltimejob;
                $astrologer->goodQuality = $req->goodQuality;
                $astrologer->biggestChallenge = $req->biggestChallenge;
                $astrologer->whatwillDo = $req->whatwillDo;
                $astrologer->videoCallRate = $req->videoCallRate;
                $astrologer->reportRate = $req->reportRate ? $req->reportRate : 0;
                $astrologer->nameofplateform = $req->nameofplateform;
                $astrologer->monthlyEarning = $req->monthlyEarning;
                $astrologer->referedPerson = $req->referedPerson;
                $astrologer->update();
                if ($req->userDeviceDetails) {
                    $userDeviceDetails = UserDeviceDetail::find($req->userId);
                    if ($userDeviceDetails) {

                        $userDeviceDetails->userId = $user->id;
                        $userDeviceDetails->appId = $req->appId;
                        $userDeviceDetails->deviceId = $req->deviceId;
                        $userDeviceDetails->fcmToken = $req->fcmToken;
                        $userDeviceDetails->deviceLocation = $req->deviceLocation;
                        $userDeviceDetails->deviceManufacturer = $req->deviceManufacturer;
                        $userDeviceDetails->deviceModel = $req->deviceModel;
                        $userDeviceDetails->appVersion = $req->appVersion;
                        $userDeviceDetails->update();
                    } else {
                        $userDeviceDetails = UserDeviceDetail::create([
                            'userId' => $req->userId,
                            'appId' => $req->appId,
                            'deviceId' => $req->deviceId,
                            'fcmToken' => $req->fcmToken,
                            'deviceLocation' => $req->deviceLocation,
                            'deviceManufacturer' => $req->deviceManufacturer,
                            'deviceModel' => $req->deviceModel,
                            'appVersion' => $req->appVersion,
                        ]);
                    }
                }
                if ($req->astrologerAvailability) {

                    $availability = DB::Table('astrologer_availabilities')
                        ->where('astrologerId', '=', $req->id)->delete();
                    $req->astrologerId = $astrologer->id;
                    foreach ($req->astrologerAvailability as $astrologeravailable) {
                        foreach ($astrologeravailable['time'] as $availability) {
                            AstrologerAvailability::create([
                                'astrologerId' => $req->id,
                                'day' => $astrologeravailable['day'],
                                'fromTime' => $availability['fromTime'],
                                'toTime' => $availability['toTime'],
                                'createdBy' => $astrologer['id'],
                                'modifiedBy' => $astrologer['id'],
                            ]);
                        }
                    }
                }
                $astrologer->astrologerAvailability = $req->astrologerAvailability;

                $astrologer->allSkill = array_map('intval', explode(',', $astrologer->allSkill));
                $astrologer->primarySkill = array_map('intval', explode(',', $astrologer->primarySkill));
                $astrologer->languageKnown = array_map('intval', explode(',', $astrologer->languageKnown));
                $astrologer->astrologerCategoryId =
                    array_map('intval', explode(',', $astrologer->astrologerCategoryId));
                $allSkill = DB::table('skills')
                    ->whereIn('id', $astrologer->allSkill)
                    ->select('name', 'id')
                    ->get();
                $primarySkill = DB::table('skills')
                    ->whereIn('id', $astrologer->primarySkill)
                    ->select('name', 'id')
                    ->get();
                $languageKnown = DB::table('languages')
                    ->whereIn('id', $astrologer->languageKnown)
                    ->select('languageName', 'id')
                    ->get();
                $catgory = DB::table('astrologer_categories')
                    ->whereIn('id', $astrologer->astrologerCategoryId)
                    ->select('name', 'id')
                    ->get();
                $astrologer->allSkill = $allSkill;
                $astrologer->primarySkill = $primarySkill;
                $astrologer->languageKnown = $languageKnown;
                $astrologer->astrologerCategoryId = $catgory;
                return response()->json([
                    'message' => 'Astrologer update sucessfully',
                    'recordList' => $astrologer,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Astrologer is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Delete astrologer
    public function deleteAstrologer(Request $req)
    {
        try {
            $astrologer = Astrologer::find($req->id);
            if ($astrologer) {
                // $data = array(
                //     'isDelete' => true,
                //     'updated_at' => Carbon::now(),
                // );
                $astrologer->delete();
                return response()->json([
                    'message' => 'Astrologer delete Sucessfully',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Astrologer is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Verify astrologer
    public function verifyAstrologer(Request $req, $id)
    {
        try {
            $astrologer = Astrologer::find($id);
            if ($astrologer) {
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                return response()->json([
                    'message' => 'Astrologer is verify',
                    'recordList' => $astrologer,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Astrologer is not verify',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Get the master data of astrologer
    public function masterAstrologer()
    {
        try {
            $skill = Skill::query();
            $skill->where('isActive', '=', true);
            $skill->where('isDelete', '=', false);
            $language = Language::query();
            $mainSourceBusiness = MainSourceOfBusiness::query();
            $highestQualification = HighestQualification::query();
            $qualifications = DegreeOrDiploma::query();
            $jobs = FulltimeJob::query();
            $countryTravel = TravelCountry::query();
            $astrologerCategory = AstrologerCategory::query();
            $astrologerCategory->where('isActive', '=', true);
            $astrologerCategory->where('isDelete', '=', false);
            return response()->json([
                'skill' => $skill->get(),
                'language' => $language->get(),
                'mainSourceBusiness' => $mainSourceBusiness->get(),
                'highestQualification' => $highestQualification->get(),
                'qualifications' => $qualifications->get(),
                'jobs' => $jobs->get(),
                'countryTravel' => $countryTravel->get(),
                'astrolgoerCategory' => $astrologerCategory->get(),
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getCounsellor(Request $req)
    {
        try {
            $skillId = DB::table('skills')
                ->where('name', '=', 'Psychologist')
                ->get();
            $id = $skillId[0]->id;
            $counsellor = DB::table('astrologers')
                ->whereRaw("find_in_set($id,allSkill)")
                ->orwhere('primarySkill', '=', $skillId[0]->id)
                ->where('isActive', '=', true)
                ->where('isDelete', '=', false)
                ->where('isVerified', '=', true);

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $counsellor->skip($req->startIndex);
                $counsellor->take($req->fetchRecord);
            }
            $isFreeAvailable = true;
            $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
            if ($isFreeChat->value == 1) {
                if ($req->userId) {
                    $isChatRequest = DB::table('chatrequest')->where('userId', $req->userId)->where('chatStatus', '=', 'Completed')->first();
                    $isCallRequest = DB::table('callrequest')->where('userId', $req->userId)->where('callStatus', '=', 'Completed')->first();
                    if ($isChatRequest || $isCallRequest) {
                        $isFreeAvailable = false;
                    } else {
                        $isFreeAvailable = true;
                    }
                }
            } else {
                $isFreeAvailable = false;
            }
            $counsellor = $counsellor->get();
            if ($counsellor && count($counsellor) > 0) {
                foreach ($counsellor as $coun) {
                    $astrologerCategory = array_map('intval', explode(',', $coun->astrologerCategoryId));
                    $allSkill = array_map('intval', explode(',', $coun->allSkill));
                    $primarySkill = array_map('intval', explode(',', $coun->primarySkill));
                    $languages = array_map('intval', explode(',', $coun->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $allSkill)
                        ->select('name')
                        ->get();
                    $skill = $allSkill->pluck('name')->all();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $primarySkill)
                        ->select('name')
                        ->get();
                    $primary = $primarySkill->pluck('name')->all();
                    $astrologerCategory = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologerCategory)
                        ->select('name')
                        ->get();
                    $astrologerCategories = $astrologerCategory->pluck('name')->all();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $languages)
                        ->select('languageName')
                        ->get();
                    $languageKnowns = $languageKnown->pluck('languageName')->all();

                    $coun->languageKnown = implode(",", $languageKnowns);
                    $coun->astrologerCategory = implode(",", $astrologerCategories);
                    $coun->allSkill = implode(",", $skill);
                    $coun->primarySkill = implode(",", $primary);
                    $coun->isFreeAvailable = $isFreeAvailable;
                }
            }
            $counsellorCount = DB::table('astrologers')
                ->whereRaw("find_in_set($id,allSkill)")
                ->orwhere('primarySkill', '=', $skillId[0]->id)
                ->where('isActive', '=', true)
                ->where('isDelete', '=', false)
                ->where('isVerified', '=', true)
                ->count();

            return response()->json([
                'recordList' => $counsellor,
                'status' => 200,
                'totalRecords' => $counsellorCount,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function checkContactNoExist(Request $req)
    {
        try {
            $data = $req->only(
                'contactNo'
            );
            $validator = Validator::make($data, [
                'contactNo' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $id = DB::table('astrologers')
                ->where('contactNo', '=', $req->contactNo)
                ->select('astrologers.id', 'astrologers.isVerified')
                ->get();
            if ($id && count($id) > 0) {
                if (!$id[0]->isVerified) {
                    return response()->json([
                        'message' => 'Your Account is not verified from admin',
                        'status' => 400,
                    ], 400);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Contact Number is Already Register',
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Contact Number is Not Register',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerById(Request $req)
    {

        try {
            $data = $req->only(
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $astrologer = DB::table('astrologers')
                ->where('id', '=', $req->astrologerId)
                ->get();

            $isFreeAvailable = true;
            $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
            if ($isFreeChat->value == 1) {
                if ($req->userId) {
                    $isChatRequest = DB::table('chatrequest')->where('userId', $req->userId)->where('chatStatus', '=', 'Completed')->first();
                    $isCallRequest = DB::table('callrequest')->where('userId', $req->userId)->where('callStatus', '=', 'Completed')->first();
                    if ($isChatRequest || $isCallRequest) {
                        $isFreeAvailable = false;
                    } else {
                        $isFreeAvailable = true;
                    }
                }
            } else {
                $isFreeAvailable = false;
            }

            // Set isFreeAvailable property for each astrologer
            foreach ($astrologer as $astro) {
                $astro->isFreeAvailable = $isFreeAvailable;
            }

            if ($astrologer) {
                $astrologer[0]->allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                $astrologer[0]->primarySkill = array_map('intval', explode(',', $astrologer[0]->primarySkill));
                $astrologer[0]->languageKnown = array_map('intval', explode(',', $astrologer[0]->languageKnown));
                $astrologer[0]->astrologerCategoryId =
                    array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                $allSkill = DB::table('skills')
                    ->whereIn('id', $astrologer[0]->allSkill)
                    ->select('name', 'id')
                    ->get();
                $primarySkill = DB::table('skills')
                    ->whereIn('id', $astrologer[0]->primarySkill)
                    ->select('name', 'id')
                    ->get();
                $languageKnown = DB::table('languages')
                    ->whereIn('id', $astrologer[0]->languageKnown)
                    ->select('languageName', 'id')
                    ->get();
                $category = DB::table('astrologer_categories')
                    ->whereIn('id', $astrologer[0]->astrologerCategoryId)
                    ->select('name', 'id')
                    ->get();
                $astrologer[0]->allSkill = $allSkill;
                $astrologer[0]->primarySkill = $primarySkill;
                $astrologer[0]->languageKnown = $languageKnown;
                $astrologer[0]->astrologerCategoryId = $category;
                $astrologerAvailability = DB::table('astrologer_availabilities')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->get();

                if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                    $day = [];
                    $working = [];
                    $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    foreach ($day as $days) {
                        $day = array(
                            'day' => $days,
                        );
                        $currentday = $days;
                        $result =
                            array_filter(
                                json_decode($astrologerAvailability),
                                function ($event) use ($currentday) {
                                    return $event->day === $currentday;
                                }
                            );
                        $ti = [];

                        foreach ($result as $available) {
                            $time = array(
                                'fromTime' => $available->fromTime,
                                'toTime' => $available->toTime,
                            );
                            array_push($ti, $time);
                        }
                        $weekDay = array(
                            'day' => $days,
                            'time' => $ti,
                        );
                        array_push($working, $weekDay);
                    }
                    $astrologer[0]->astrologerAvailability = $working;
                } else {
                    $astrologer[0]->astrologerAvailability = [];
                }

                $chatHistory = DB::Table('chatrequest as chat')
                    ->join('users', 'users.id', '=', 'chat.userId')
                    ->join('astrologers as astr', 'astr.id', '=', 'chat.astrologerId')
                    ->select(
                        'chat.*',
                        'users.name',
                        'users.contactNo',
                        'users.profile',
                        'astr.name as astrologerName',
                        'astr.charge'
                    )
                    ->where('chat.astrologerId', '=', $req->astrologerId)
                    ->where('chat.chatStatus', '=', "Completed")
                    ->orderBy('chat.id', 'DESC')
                    ->get();
                $callHistory = DB::Table('callrequest as call')
                    ->join('users as us', 'us.id', '=', 'call.userId')
                    ->join('astrologers as ae', 'ae.id', '=', 'call.astrologerId')
                    ->select(
                        'call.*',
                        'us.name',
                        'us.contactNo',
                        'us.profile',
                        'ae.name as astrologerName',
                        'ae.charge'
                    )
                    ->where('call.astrologerId', '=', $req->astrologerId)
                    ->where('call.callStatus', '=', "Completed")
                    ->orderBy('call.id', 'DESC')
                    ->get();


                $wallet = DB::table('wallettransaction as wallet')
                    ->leftjoin('order_request', 'order_request.id', '=', 'wallet.orderId')
                    ->leftjoin('users', 'users.id', '=', 'order_request.userId')
                    ->select('wallet.*', 'users.name', 'order_request.totalMin')
                    ->where('wallet.userId', '=', $astrologer[0]->userId)
                    ->orderBy('wallet.id', 'DESC')
                    ->get();

                if ($wallet && count($wallet) > 0) {
                    for ($i = 0; $i < count($wallet); $i++) {
                        if ($wallet[$i]->transactionType == 'Gift') {
                            $user = DB::table('users')->where('id', $wallet[$i]->createdBy)->select('name')->first();

                            if ($user) {
                                $wallet[$i]->name = $user->name;
                            } else {
                                $wallet[$i]->name = 'Unknown'; // Or handle the missing user case as needed
                            }
                        }
                    }
                }

                $review = DB::table('user_reviews as ur')
                    ->join('users as us', 'us.id', '=', 'ur.userId')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->select('ur.*', 'us.name as userName', 'us.profile')
                    ->orderBy('ur.id', 'DESC')
                    ->get();

                $reports = DB::table('user_reports as ur')
                    ->join('users as u', 'u.id', '=', 'ur.userId')
                    ->join('report_types', 'report_types.id', '=', 'ur.reportType')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->select(
                        'ur.*',
                        'u.name',
                        'u.profile',
                        'u.contactNo',
                        'report_types.reportImage',
                        'report_types.title as reportType'
                    )
                    ->orderBy('ur.id', 'DESC')
                    ->get();

                $callMin = DB::table('callrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('totalMin');

                $chatMin = DB::table('chatrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('totalMin');

                $payment = DB::table('payment')
                    ->join('astrologers', 'astrologers.userId', 'payment.userId')
                    ->where('astrologers.id', $req->astrologerId)
                    ->select('payment.*')
                    ->orderBy('payment.id', 'DESC')
                    ->get();


                $fiveStarRating = 0;
                $fourStarRating = 0;
                $threeStarRating = 0;
                $twoStarRating = 0;
                $oneStarRating = 0;
                if ($review && count($review) > 0) {
                    for ($i = 0; $i < count($review); $i++) {

                        if ($review[$i]->rating == 1) {
                            $oneStarRating += 1;
                        }
                        if ($review[$i]->rating == 2) {
                            $twoStarRating += 1;
                        }
                        if ($review[$i]->rating == 3) {
                            $threeStarRating += 1;
                        }
                        if ($review[$i]->rating == 4) {
                            $fourStarRating += 1;
                        }
                        if ($review[$i]->rating == 5) {
                            $fiveStarRating += 1;
                        }
                    }
                }
                $starRating = $oneStarRating + $twoStarRating + $threeStarRating + $fourStarRating + $fiveStarRating;
                $avgRating = $review && count($review) > 0 ? $starRating / count($review) : 0;
                $rating = array(
                    'oneStarRating' => $oneStarRating > 0 ? $oneStarRating * 100 / count($review) : 0,
                    'twoStarRating' => $twoStarRating > 0 ? $twoStarRating * 100 / count($review) : 0,
                    'threeStarRating' => $threeStarRating > 0 ? $threeStarRating * 100 / count($review) : 0,
                    'fourStarRating' => $fourStarRating > 0 ? $fourStarRating * 100 / count($review) : 0,
                    'fiveStarRating' => $fiveStarRating > 0 ? $fiveStarRating * 100 / count($review) : 0,
                );
                $astrologer[0]->chatHistory = $chatHistory;
                $astrologer[0]->callHistory = $callHistory;
                $astrologer[0]->wallet = $wallet;
                $astrologer[0]->payment = $payment;
                $astrologer[0]->review = $review;
                $astrologer[0]->report = $reports;
                $astrologer[0]->chatMin = $chatMin;
                $astrologer[0]->callMin = $callMin;
                $astrologer[0]->astrologerRating = $rating;
                $astrologer[0]->rating = $avgRating;
                $astrologer[0]->ratingcount = count($review);


                return response()->json([
                    "message" => "get Astrologer Profile",
                    "recordList" => $astrologer,
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function searchAstro(Request $req)
    {
        try {
            if ($req->filterKey == 'astromall') {
                $result = DB::table('astromall_products')
                    ->whereRaw(sql: "name LIKE '%" . $req->searchString . "%' ");
                // ->get();
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $result = $result->skip($req->startIndex);
                    $result = $result->take($req->fetchRecord);
                }
                $result = $result->get();
            } elseif ($req->filterKey == 'astrologer') {
                $result = DB::table('astrologers')
                    ->whereRaw(sql: "name LIKE '%" . $req->searchString . "%' ");
                // ->get();
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $result = $result->skip($req->startIndex);
                    $result = $result->take($req->fetchRecord);
                }
                $result = $result->get();
                if ($result && count($result) > 0) {
                    foreach ($result as $astro) {
                        // Retrieve user reviews for the current astrologer
                        $reviews = DB::table('user_reviews')
                            ->where('astrologerId', '=', $astro->id)
                            ->get();

                        if ($reviews && count($reviews) > 0) {
                            $avgRating = 0;
                            foreach ($reviews as $review) {
                                $avgRating += $review->rating;
                            }
                            $avgRating = $avgRating / count($reviews);


                            $astro->rating = $avgRating;
                        } else {
                            $astro->rating = 0;
                        }
                    }
                }
                $isFreeAvailable = true;
                $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
                if ($isFreeChat->value == 1) {
                    if ($req->userId) {
                        $isChatRequest = DB::table('chatrequest')->where('userId', $req->userId)->where('chatStatus', '=', 'Completed')->first();
                        $isCallRequest = DB::table('callrequest')->where('userId', $req->userId)->where('callStatus', '=', 'Completed')->first();
                        if ($isChatRequest || $isCallRequest) {
                            $isFreeAvailable = false;
                        } else {
                            $isFreeAvailable = true;
                        }
                    }
                } else {
                    $isFreeAvailable = false;
                }
                if ($result && count($result) > 0) {
                    for ($i = 0; $i < count($result); $i++) {
                        $astrologerCategory = array_map('intval', explode(',', $result[$i]->astrologerCategoryId));
                        $allSkill = array_map('intval', explode(',', $result[$i]->allSkill));
                        $primarySkill = array_map('intval', explode(',', $result[$i]->primarySkill));
                        $languages = array_map('intval', explode(',', $result[$i]->languageKnown));
                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $primarySkill = DB::table('skills')
                            ->whereIn('id', $primarySkill)
                            ->select('name')
                            ->get();
                        $primarySkill = $primarySkill->pluck('name')->all();
                        $astrologerCategory = DB::table('astrologer_categories')
                            ->whereIn('id', $astrologerCategory)
                            ->select('name')
                            ->get();
                        $astrologerCategory = $astrologerCategory->pluck('name')->all();
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnown = $languageKnown->pluck('languageName')->all();

                        $result[$i]->languageKnown = implode(",", $languageKnown);
                        $result[$i]->astrologerCategoryId = implode(",", $astrologerCategory);
                        $result[$i]->allSkill = implode(",", $skill);
                        $result[$i]->primarySkill = implode(",", $primarySkill);
                        $result[$i]->isFreeAvailable = $isFreeAvailable;
                    }
                }
            }

            return response()->json([
                'recordList' => $result,
                'status' => 200,
                'message' => 'Get Search AStro',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerForCustomer(Request $req)
    {
        try {
            $data = $req->only(
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $astrologer = DB::table('astrologers')
                ->where('id', '=', $req->astrologerId)
                ->get();
            if ($astrologer && count($astrologer) > 0) {
                $astrologerCategory = array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                $allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                $primary = array_map('intval', explode(',', $astrologer[0]->primarySkill));
                $languages = array_map('intval', explode(',', $astrologer[0]->languageKnown));
                $allSkill = DB::table('skills')
                    ->whereIn('id', $allSkill)
                    ->select('name')
                    ->get();
                $skill = $allSkill->pluck('name')->all();
                $primarySkills = DB::table('skills')
                    ->whereIn('id', $primary)
                    ->select('name', 'id')
                    ->get();
                $primarySkill = $primarySkills->pluck('name')->all();
                $astrologerCategory = DB::table('astrologer_categories')
                    ->whereIn('id', $astrologerCategory)
                    ->select('name')
                    ->get();
                $astrologerCategory = $astrologerCategory->pluck('name')->all();
                $languageKnown = DB::table('languages')
                    ->whereIn('id', $languages)
                    ->select('languageName')
                    ->get();
                $languageKnown = $languageKnown->pluck('languageName')->all();
                if ($req->userId) {
                    $follow = DB::table('astrologer_followers')
                        ->where('userId', '=', $req->userId)
                        ->where('astrologerId', '=', $req->astrologerId)
                        ->get();
                    if ($follow && count($follow) > 0) {
                        $astrologer[0]->isFollow = true;
                    } else {
                        $astrologer[0]->isFollow = false;
                    }
                    $block = DB::table('blockastrologer')
                        ->where('userId', '=', $req->userId)
                        ->where('astrologerId', '=', $req->astrologerId)
                        ->get();
                    if ($block && count($block) > 0) {
                        $astrologer[0]->isBlock = true;
                    } else {
                        $astrologer[0]->isBlock = false;
                    }
                } else {
                    $astrologer[0]->isFollow = false;
                    $astrologer[0]->isBlock = false;
                }
                $callMin = DB::table('callrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('totalMin');

                $chatMin = DB::table('chatrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('totalMin');

                $follower = DB::table('astrologer_followers')
                    ->join('users', 'users.id', '=', 'astrologer_followers.userId')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->count();


                $fiveStarRating = 0;
                $fourStarRating = 0;
                $threeStarRating = 0;
                $twoStarRating = 0;
                $oneStarRating = 0;
                $review = DB::table('user_reviews')
                    ->join('users as u', 'u.id', '=', 'user_reviews.userId')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->select('user_reviews.*', 'u.name as userName', 'u.profile')
                    ->orderBy('user_reviews.id', 'DESC')
                    ->get();
                if ($review && count($review) > 0) {
                    for ($i = 0; $i < count($review); $i++) {
                        if (round($review[$i]->rating) == 1) {
                            $oneStarRating += 1;
                        }
                        if (round($review[$i]->rating) == 2) {
                            $twoStarRating += 1;
                        }
                        if (round($review[$i]->rating) == 3) {
                            $threeStarRating += 1;
                        }
                        if (round($review[$i]->rating) == 4) {
                            $fourStarRating += 1;
                        }
                        if (round($review[$i]->rating) == 5) {
                            $fiveStarRating += 1;
                        }
                    }
                }
                $rating = array(
                    'oneStarRating' => $oneStarRating,
                    'twoStarRating' => $twoStarRating,
                    'threeStarRating' => $threeStarRating,
                    'fourStarRating' => $fourStarRating,
                    'fiveStarRating' => $fiveStarRating,
                );
                $isFreeAvailable = true;
                $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
                if ($isFreeChat->value == 1) {
                    if ($req->userId) {
                        $isChatRequest = DB::table('chatrequest')->where('userId', $req->userId)->where('chatStatus', '=', 'Completed')->first();
                        $isCallRequest = DB::table('callrequest')->where('userId', $req->userId)->where('callStatus', '=', 'Completed')->first();
                        if ($isChatRequest || $isCallRequest) {
                            $isFreeAvailable = false;
                        } else {
                            $isFreeAvailable = true;
                        }
                    }
                } else {
                    $isFreeAvailable = false;
                }
                $avgRating = DB::table('user_reviews')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->groupBy('astrologerId')
                    ->avg('rating');

                $consultant = DB::table('astrologers')->where('isActive', 1)->where('isVerified', 1)->where('isDelete', 0)->where('id', '!=', $req->astrologerId)->select('profileImage', 'name', 'charge', 'primarySkill', 'id')->orderBy('id', 'DESC')->get();
                $similiar = [];
                if ($consultant && count($consultant) > 0) {
                    for ($i = 0; $i < count($consultant); $i++) {
                        $consultantPrimarySkill = array_map('intval', explode(',', $consultant[$i]->primarySkill));
                        $ifExist = !empty(array_intersect($primary, $consultantPrimarySkill));
                        if ($ifExist) {
                            array_push($similiar, $consultant[$i]);
                        }
                        if ($similiar && count($similiar) == 3) {
                            break;
                        }
                    }
                    if (($similiar && count($similiar) < 3) || count($similiar) == 0) {
                        $index = 0;

                        for ($j = count($similiar); $j < 3; $j++) {
                            // Check if the index is within the bounds of the array
                            if ($index < count($consultant)) {
                                $id = $consultant[$index]->id;
                                $result = array_filter($similiar, function ($event) use ($id) {
                                    return $event->id === $id;
                                });

                                if ($result && count($result) == 0) {
                                    array_push($similiar, $consultant[$index]);
                                }

                                $index += 1;
                            }
                        }
                    }
                }

                $astrologer[0]->languageKnown = implode(",", $languageKnown);
                $astrologer[0]->astrologerCategoryId = implode(",", $astrologerCategory);
                $astrologer[0]->allSkill = implode(",", $skill);
                $astrologer[0]->primarySkill = implode(",", $primarySkill);
                $astrologer[0]->chatMin = $chatMin;
                $astrologer[0]->callMin = $callMin;
                $astrologer[0]->follower = $follower;
                $astrologer[0]->astrologerRating = $rating;
                $astrologer[0]->rating = $avgRating;
                $astrologer[0]->isFreeAvailable = $isFreeAvailable;
                $astrologer[0]->similiarConsultant = $similiar;
            }
            return response()->json([
                "message" => "get Astrologer Profile",
                "recordList" => $astrologer,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function reportblockAstrologer(request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'astrologerId',
                'reason',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'reason' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $reportBlock = array(
                'astrologerId' => $req->astrologerId,
                'userId' => $id,
                'reason' => $req->reason,
            );
            DB::table('blockastrologer')->insert($reportBlock);
            return response()->json([
                "message" => "Block Astrologer",
                'status' => 200,
            ], 200);
            // $reports = DB::table('blockastrologer')

        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function unblockAstrologer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            DB::table('blockastrologer')
                ->where('userId', '=', $id)
                ->where('astrologerId', '=', $req->astrologerId)
                ->delete();
            return response()->json([
                "message" => "UnBlock Astrologer",
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getBlockAstrologer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $blockAstrologer = DB::table('blockastrologer')->where('userId', '=', $id)->get();
            if ($blockAstrologer && count($blockAstrologer) > 0) {
                foreach ($blockAstrologer as $block) {
                    $astrologer = Astrologer::find($block->astrologerId);
                    $astrologerCategory = array_map('intval', explode(',', $astrologer->astrologerCategoryId));
                    $allSkill = array_map('intval', explode(',', $astrologer->allSkill));
                    $primarySkill = array_map('intval', explode(',', $astrologer->primarySkill));
                    $languages = array_map('intval', explode(',', $astrologer->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $allSkill)
                        ->select('name')
                        ->get();
                    $skill = $allSkill->pluck('name')->all();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $primarySkill)
                        ->select('name')
                        ->get();
                    $primarySkill = $primarySkill->pluck('name')->all();
                    $astrologerCategory = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologerCategory)
                        ->select('name')
                        ->get();
                    $astrologerCategory = $astrologerCategory->pluck('name')->all();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $languages)
                        ->select('languageName')
                        ->get();
                    $languageKnown = $languageKnown->pluck('languageName')->all();
                    $block->languageKnown = implode(",", $languageKnown);
                    $block->astrologerCategoryId = implode(",", $astrologerCategory);
                    $block->allSkill = implode(",", $skill);
                    $block->primarySkill = implode(",", $primarySkill);
                    $block->profile = $astrologer->profileImage;
                    $block->astrologerName = $astrologer->name;
                    $block->experienceInYears = $astrologer->experienceInYears;
                }
            }
            return response()->json([
                "message" => "Get Block Astrologer",
                'status' => 200,
                'recordList' => $blockAstrologer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getUserById(Request $req)
    {
        try {


            $data = $req->only([
                'userId',
            ]);
            $validator = Validator::make($data, [
                'userId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors(), 'status' => 400], 400);
            }

            $user = DB::table('users')
                ->where('id', '=', $req->userId)
                ->get();
            if ($user) {
                $follower = DB::table('astrologer_followers')
                    ->join('astrologers', 'astrologer_followers.astrologerId', '=', 'astrologers.id')
                    ->where('astrologer_followers.userId', '=', $req->userId)
                    ->select('astrologers.*', 'astrologer_followers.created_at as followingDate')
                    ->orderBy('astrologer_followers.id', 'DESC')
                    ->get();
                if ($follower && count($follower) > 0) {
                    foreach ($follower as $follow) {
                        $languages = DB::table('languages')
                            ->whereIn('id', explode(',', $follow->languageKnown))
                            ->select('languageName')
                            ->get();

                        $allSkill = DB::table('skills')
                            ->whereIn('id', explode(',', $follow->languageKnown))
                            ->get('name');
                        $follow->languageKnown = $languages;
                        $follow->allSkill = $allSkill;
                    }
                }
                $orderRequest = DB::table('order_request')
                    ->join('product_categories', 'product_categories.id', '=', 'order_request.productCategoryId')
                    ->join('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                    ->join('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                    ->where('order_request.userId', '=', $req->userId)
                    ->where('order_request.orderType', '=', 'astromall');


                $orderRequestCount = $orderRequest->count();
                $orderRequest->select(
                    'order_request.*',
                    'product_categories.name as productCategory',
                    'astromall_products.productImage',
                    'astromall_products.amount as productAmount',
                    'astromall_products.description',
                    'order_addresses.name as orderAddressName',
                    'order_addresses.phoneNumber',
                    'order_addresses.flatNo',
                    'order_addresses.locality',
                    'order_addresses.landmark',
                    'order_addresses.city',
                    'order_addresses.state',
                    'order_addresses.country',
                    'order_addresses.pincode',
                    'astromall_products.name as productName'
                )->addSelect(DB::raw("CONCAT('" . route('order.invoice', '') . "/', order_request.id) as invoice_link"));

                $orderRequest->orderBy('order_request.id', 'DESC');
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $orderRequest->skip($req->startIndex);
                    $orderRequest->take($req->fetchRecord);
                }
                $orderRequest = $orderRequest->get();

                $giftList = DB::table('astrologer_gifts')
                    ->join('gifts', 'gifts.id', 'astrologer_gifts.giftId')
                    ->join('astrologers', 'astrologers.id', '=', 'astrologer_gifts.astrologerId')
                    ->where('astrologer_gifts.userId', '=', $req->userId);

                $giftListCount = $giftList->count();
                $giftList->select('gifts.name as giftName', 'astrologer_gifts.*', 'astrologers.id as astrologerId', 'astrologers.name as astrolgoerName', 'astrologers.contactNo');

                $giftList->orderBy('astrologer_gifts.id', 'DESC');
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $giftList->skip($req->startIndex);
                    $giftList->take($req->fetchRecord);
                }
                $giftList = $giftList->get();

                $chatHistory = DB::Table('chatrequest')
                    ->join('astrologers as astr', 'astr.id', '=', 'chatrequest.astrologerId')
                    ->where('chatrequest.userId', '=', $req->userId)
                    ->where('chatrequest.chatStatus', '=', 'Completed');

                $chatHistoryCount = $chatHistory->count();
                $chatHistory->select(
                    'chatrequest.*',
                    'astr.id as astrologerId',
                    'astr.name as astrologerName',
                    'astr.contactNo',
                    'astr.profileImage',
                    'astr.charge'
                );
                $chatHistory->orderBy('chatrequest.id', 'DESC');
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $chatHistory->skip($req->startIndex);
                    $chatHistory->take($req->fetchRecord);
                }
                $chatHistory = $chatHistory->get();
                if ($chatHistory && count($chatHistory) > 0) {
                    for ($i = 0; $i < count($chatHistory); $i++) {
                        $chatHistory[$i]->isFreeSession = $chatHistory[$i]->isFreeSession ? true : false;
                    }
                }
                $callHistory = DB::Table('callrequest')
                    ->join('astrologers as astro', 'astro.id', '=', 'callrequest.astrologerId')

                    ->where('callrequest.userId', '=', $req->userId)
                    ->where('callrequest.callStatus', '=', 'Completed');
                $callHistoryCount = $callHistory->count();
                $callHistory->select('callrequest.*', 'astro.id as astrologerId', 'astro.name as astrologerName', 'astro.contactNo', 'astro.profileImage', 'astro.charge');
                $callHistory->orderBy('callrequest.id', 'DESC');

                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $callHistory->skip($req->startIndex);
                    $callHistory->take($req->fetchRecord);
                }
                $callHistory = $callHistory->get();
                if ($callHistory && count($callHistory) > 0) {
                    for ($i = 0; $i < count($callHistory); $i++) {
                        $callHistory[$i]->isFreeSession = $callHistory[$i]->isFreeSession ? true : false;
                    }
                }
                $reportHistory = DB::Table('user_reports')
                    ->join('astrologers as astro', 'astro.id', '=', 'user_reports.astrologerId')
                    ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                    ->where('user_reports.userId', '=', $req->userId);

                $reportHistoryCount = $reportHistory->count();

                $reportHistory->select('user_reports.*', 'astro.id as astrologerId', 'astro.name as astrologerName', 'astro.contactNo', 'report_types.title', 'astro.profileImage', 'astro.reportRate');

                $reportHistory->orderBy('user_reports.id', 'DESC');
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $reportHistory->skip($req->startIndex);
                    $reportHistory->take($req->fetchRecord);
                }
                $reportHistory = $reportHistory->get();
                if ($reportHistory && count($reportHistory) > 0) {
                    for ($i = 0; $i < count($reportHistory); $i++) {
                        if (!$reportHistory[$i]->reportFile) {
                            $reportHistory[$i]->isFileUpload = false;
                        } else {
                            $reportHistory[$i]->isFileUpload = true;
                        }
                    }
                }
                $wallet = DB::table('wallettransaction')
                    ->leftjoin('order_request', 'order_request.id', '=', 'wallettransaction.orderId')
                    ->leftjoin('astrologers', 'astrologers.id', '=', 'wallettransaction.astrologerId')
                    ->where('wallettransaction.userId', '=', $req->userId);
                $walletCount = $wallet->count();
                $wallet->select('wallettransaction.*', 'astrologers.name', 'order_request.totalMin');
                $wallet->orderBy('wallettransaction.id', 'DESC');
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $wallet->skip($req->startIndex);
                    $wallet->take($req->fetchRecord);
                }
                $wallet = $wallet->get();

                $payment = DB::table('payment')
                    ->where('userId', '=', $req->userId)
                    ->orderBy('id', 'DESC');
                $paymentCount = $payment->count();
                if ($req->startIndex >= 0 && $req->fetchRecord) {
                    $payment->skip($req->startIndex);
                    $payment->take($req->fetchRecord);
                }
                $payment = $payment->get();

                $notification = DB::table('user_notifications')
                    ->where('userId', '=', $req->userId)
                    ->get();
                $orderRequests = array(
                    'totalCount' => $orderRequestCount,
                    'order' => $orderRequest,
                );
                $giftLists = array(
                    'totalCount' => $giftListCount,
                    'gifts' => $giftList,
                );
                $chatHistorys = array(
                    'totalCount' => $chatHistoryCount,
                    'chatHistory' => $chatHistory,
                );
                $callHistorys = array(
                    'totalCount' => $callHistoryCount,
                    'callHistory' => $callHistory,
                );
                $reportHistorys = array(
                    'totalCount' => $reportHistoryCount,
                    'reportHistory' => $reportHistory,
                );
                $wallets = array(
                    'totalCount' => $walletCount,
                    'wallet' => $wallet,
                );
                $payments = array(
                    'totalCount' => $paymentCount,
                    'payment' => $payment,
                );
                $user[0]->follower = $follower;
                $user[0]->orders = $orderRequests;
                $user[0]->sendGifts = $giftLists;
                $user[0]->chatRequest = $chatHistorys;
                $user[0]->callRequest = $callHistorys;
                $user[0]->reportRequest = $reportHistorys;
                $user[0]->walletTransaction = $wallets;
                $user[0]->paymentLogs = $payments;
                $user[0]->notification = $notification;

                return response()->json([
                    "message" => "Get User Successfully",
                    "status" => 200,
                    "recordList" => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function validateSession(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            } else {
                $user = Auth::guard('api')->user();
                $user->sessionToken = $req->header('Authorization');
                $user->token_type = 'Bearer';

                return response()->json([
                    'status' => 200,
                    'recordList' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function validateSessionForAstrologer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            } else {
                $astrologer = DB::Table('astrologers')->where('userId', '=', Auth::guard('api')->user()->id)->get();
                if ($astrologer) {
                    $astrologer[0]->allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                    $astrologer[0]->primarySkill = array_map('intval', explode(',', $astrologer[0]->primarySkill));
                    $astrologer[0]->languageKnown = array_map('intval', explode(',', $astrologer[0]->languageKnown));
                    $astrologer[0]->astrologerCategoryId =
                        array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->allSkill)
                        ->select('name', 'id')
                        ->get();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->primarySkill)
                        ->select('name', 'id')
                        ->get();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $astrologer[0]->languageKnown)
                        ->select('languageName', 'id')
                        ->get();
                    $category = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologer[0]->astrologerCategoryId)
                        ->select('name', 'id')
                        ->get();
                    $astrologer[0]->allSkill = $allSkill;
                    $astrologer[0]->primarySkill = $primarySkill;
                    $astrologer[0]->languageKnown = $languageKnown;
                    $astrologer[0]->astrologerCategoryId = $category;
                    $astrologerAvailability = DB::table('astrologer_availabilities')
                        ->where('astrologerId', '=', $astrologer[0]->id)
                        ->get();
                    $working = [];
                    if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                        $day = [];

                        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($day as $days) {
                            $day = array(
                                'day' => $days,
                            );
                            $currentday = $days;
                            $result =
                                array_filter(json_decode($astrologerAvailability), function ($event) use ($currentday) {
                                    return $event->day === $currentday;
                                });
                            $ti = [];

                            foreach ($result as $available) {
                                $time = array(
                                    'fromTime' => $available->fromTime,
                                    'toTime' => $available->toTime,
                                );
                                array_push($ti, $time);
                            }
                            $weekDay = array(
                                'day' => $days,
                                'time' => $ti,
                            );
                            array_push($working, $weekDay);
                        }
                    }
                    $astrologer[0]->astrologerAvailability = $working;
                }
                $astrologer[0]->sessionToken = $req->header('Authorization');
                $astrologer[0]->token_type = 'Bearer';

                return response()->json([
                    'status' => 200,
                    'recordList' => $astrologer[0],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function getUserProfile(Request $req)
    {
        try {
            $userProfile = DefaultProfile::query()->where('isActive', true)->get();
            return response()->json([
                'status' => 200,
                "message" => "Get Profile Successfully",
                'recordList' => $userProfile,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getUserdetails()
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = Auth::guard('api')->user();
            $userWallet = UserWallet::query()
                ->where('userId', '=', $data['id'])
                ->get();
            if ($userWallet && count($userWallet) > 0) {
                $data->totalWalletAmount = $userWallet[0]->amount;
            } else {
                $data->totalWalletAmount = 0;
            }
            return response()->json([
                'status' => 200,
                "message" => "Get Profile Successfully",
                'userDetails' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerdetails()
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = Auth::guard('api')->user();
            $userWallet = UserWallet::query()
                ->where('userId', '=', $data['id'])
                ->get();

            $astrologer = DB::table('astrologers')->where('astrologers.userId', '=', $data['id'])->select('astrologers.id as astrologerId')->get();

            if ($userWallet && count($userWallet) > 0) {
                $data->totalWalletAmount = $userWallet[0]->amount;
            } else {
                $data->totalWalletAmount = 0;
            }

            if ($astrologer && count($astrologer) > 0) {
                $data->astrologerId = $astrologer[0]->astrologerId;
            } else {
                $data->astrologerId = 0;
            }

            return response()->json([
                'status' => 200,
                "message" => "Get Profile Successfully",
                'userDetails' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function checkContactNoExistForUser(Request $req)
    {
        try {

            $data = $req->only(
                'contactNo',
                'fromApp',
                'type'
            );
            $validator = Validator::make($data, [
                'fromApp' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            if (empty($req->contactNo) && empty($req->email)) {
                return response()->json([
                    'error' => "Please enter your Email or Contact Number",
                    'status' => 400,
                ], 400);
            }


            $fromApp = strtolower(!empty($req->fromApp) ? $req->fromApp : 'user');
            $type = strtolower(!empty($req->type) ? $req->type : 'login');

            if (strtolower($fromApp) == 'astrologer' && $type == 'register') {

                if (!empty($req->contactNo) && empty($req->email)) {
                    $userdata = DB::table('users')->where('contactNo', '=', $req->contactNo)->select('users.*')->get();

                    if ($userdata && count($userdata) > 0) {
                        return response()->json([
                            'message' => 'This Mobile number is already registerd',
                            'status' => 400,
                        ], 400);
                    }

                    $astrologerdata = DB::table('astrologers')->where('contactNo', '=', $req->contactNo)->select('astrologers.*')->get();

                    if ($astrologerdata && count($astrologerdata) > 0) {
                        return response()->json([
                            'message' => 'This Mobile number is already registerd',
                            'status' => 400,
                        ], 400);
                    }
                }

                if (!empty($req->email) && empty($req->contactNo)) {

                    $userdata = DB::table('users')->where('email', '=', $req->email)->select('users.*')->get();

                    if ($userdata && count($userdata) > 0) {
                        return response()->json([
                            'message' => 'This email is already registerd',
                            'status' => 400,
                        ], 400);
                    }

                    $astrologerdata = DB::table('astrologers')->where('email', '=', $req->email)->select('astrologers.*')->get();

                    if ($astrologerdata && count($astrologerdata) > 0) {
                        return response()->json([
                            'message' => 'This email is already registerd',
                            'status' => 400,
                        ], 400);
                    }
                }
            }

            if (strtolower($fromApp) == 'astrologer' && $type == 'login') {
                $astrologerdata = DB::table('astrologers')->where('contactNo', '=', $req->contactNo)->select('astrologers.*')->get();

                if (count($astrologerdata) <= 0) {
                    return response()->json([
                        'message' => 'This Mobile number is not already registerd',
                        'status' => 400,
                    ], 400);
                }

                if ($astrologerdata[0]->isVerified != 1) {
                    return response()->json([
                        'message' => 'Your Account is not verified from admin',
                        'status' => 400,
                    ], 400);
                }
            }

            $otp = strval(random_int(100000, 999999));

            if (!empty($req->contactNo)) {

                $msg91AuthKey = DB::table('systemflag')->where('name', 'msg91AuthKey')->first();
                $msg91SendOtpTemplateId = DB::table('systemflag')->where('name', 'msg91SendOtpTemplateId')->first();

                $msg91AuthKey = $msg91AuthKey->value;

                $payload = [
                    "template_id" => (string) $msg91SendOtpTemplateId->value,
                    "mobile" => "91$req->contactNo",
                    "authkey" => (string) $msg91AuthKey,
                    "realTimeResponse" => "1",
                    "otp_length" => "6",
                    "otp" => $otp,
                ];

                // Remove null values from payload
                $payload = array_filter($payload, fn($v) => !is_null($v));

                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL =>  "https://control.msg91.com/api/v5/otp?otp_expiry=&template_id=&mobile=&authkey=&realTimeResponse",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/JSON",
                        "content-type: application/json"
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                $resData = json_decode($response, true);

                if (!empty($resData['type']) && $resData['type'] != 'success') {
                    return response()->json([
                        'message' => 'Failed to send OTP',
                        'status' => 400,
                    ], 400);
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Contact Number is Not Register',
                    'otp' => !empty($req->fromWeb) ? base64_encode($otp) : $otp,
                    'msg91Data' => $resData,
                ], 200);
            }

            if (!empty($req->email)) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Email address is Not Register',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
