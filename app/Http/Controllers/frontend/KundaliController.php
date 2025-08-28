<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;


class KundaliController extends Controller
{
    public function getPanchang(Request $request)
    {
        Artisan::call('cache:clear');
        // $panchangDate=$request->panchangDate?:Carbon::now();


        // $getPanchang = Http::withoutVerifying()->post(url('/') . '/api/get/panchang', [
        //     'panchangDate' => $panchangDate,
        // ])->json();
        // dd($getPanchang);
        $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();
        $ip = $request->ip();
            if ($ip === '127.0.0.1' || $ip === '::1' || !$ip) {
                $ip = '103.238.108.209';
            }

        $geoResponse = Http::get("http://ip-api.com/json/{$ip}");
        $geoData = $geoResponse->json();

        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
        $timezone = $geoData['timezone'];

        $date = date('d/m/Y');
            if($request->panchangDate){
                $date = date('d/m/Y',strtotime($request->panchangDate));
            }
        $time = now($timezone)->format('H:i');


        $Todayspanchang = Http::get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
            'date' => $date,
            'time' => urlencode($time),
            'tz' => $this->getTimezoneOffset($timezone),
            'lat' => $latitude,
            'lon' => $longitude,
             'api_key' => $api_key->value,
            'lang' => 'en'
        ]);

        $getPanchang = $Todayspanchang->json();
        //  if($getPanchang['status']==400){
        //     print_r('out of api calls - renew subscription');die;
        // }


        return view('frontend.pages.panchang', [
            'getPanchang' => $getPanchang,

        ]);
    }

    private function getTimezoneOffset($timezone)
    {
        $time = new \DateTime('now', new \DateTimeZone($timezone));
        return $time->getOffset() / 3600; // Convert seconds to hours
    }

    public function getkundali(Request $request)
    {
        Artisan::call('cache:clear');

        $session = new Session();
        $token = $session->get('token');


        $getkundaliprice = Http::withoutVerifying()->post(url('/') . '/api/pdf/price', [
            'token' => $token,
        ])->json();

        $getkundali = Http::withoutVerifying()->post(url('/') . '/api/getkundali', [
            'token' => $token,
        ])->json();

        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
            // dd( $getkundaliprice);

        return view('frontend.pages.kundali', [
            'getkundali' => $getkundali,
            'getkundaliprice' => $getkundaliprice,
            'currency' => $currency,

        ]);
    }

    public function kundaliMatch(Request $request)
    {

        return view('frontend.pages.kundali-matching', [


        ]);
    }

    public function kundaliMatchReport(Request $request)
    {
        $KundaliMatching = Http::withoutVerifying()->post(url('/') . '/api/KundaliMatching/report', [
            'male_kundli_id' => $request->male_kundli_id,
            'female_kundli_id' => $request->female_kundli_id,
        ])->json();

        $kundalimale = Kundali::where('id', $request->male_kundli_id)->first();
        $kundalifemale = Kundali::where('id', $request->female_kundli_id)->first();
        // dd($kundalimale);

        return view('frontend.pages.kundali-match-report', [
            'KundaliMatching' => $KundaliMatching,
            'kundalimale' => $kundalimale,
            'kundalifemale' => $kundalifemale,

        ]);
    }


    public function kundaliReport(Request $request)
    {
         $KundaliReport = Http::withoutVerifying()->post(url('/') . '/api/kundali/getKundaliReport', [
            'kundali_id' => $request->kundali_id,
            'lang'=>$request->lang
        ])->json();
        // dd($KundaliReport);
        return view('frontend.pages.kundali-report',compact('KundaliReport'));
    }


}
