<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerGift;
use App\Models\UserModel\Gift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\services\FCMService;

class GiftController extends Controller
{

    //Get all the gift
    public function getGifts(Request $req)
    {
        try {

            $gift = Gift::query();
            if ($s = $req->input(key:'s')) {
                $gift->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            $giftCount = $gift->count();
            $gift->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $gift->skip($req->startIndex);
                $gift->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $gift->get(),
                'status' => 200,
                'totalRecords' => $giftCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show only active blog
    public function activeGifts()
    {
        try {
            $gift = Gift::query()->where('isActive', '=', '1');
            return response()->json([
                'recordList' => $gift->get(),
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function sendGifts(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'giftId',
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'giftId' => 'required',
                'astrologerId' => 'required',

            ]);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            DB::beginTransaction();

            $gift = DB::Table('gifts')
            ->where('id', '=', $req->giftId)
            ->get();

            $userWallet = DB::table('user_wallets')
            ->where('userId', '=', $id)
            ->get();

            if($gift[0]->amount > $userWallet[0]->amount){
                return response()->json([
                    'status' => 400,
                    'message' => 'Insufficient Balance in your wallet',
                ], 200);
            }

            // Gift Commision
            $deduction = $gift[0]->amount;
            $commission = DB::table('commissions')
                ->where('commissionTypeId', '=', '5')
                ->where('astrologerId', '=', $req->astrologerId)
                ->get();
            if ($commission && count($commission) > 0) {
                $adminCommission = ($commission[0]->commission * $deduction) / 100;
            } else {
                $syscommission = DB::table('systemflag')->where('name', 'GiftCommission')->select('value')->get();

                $adminCommission = ($syscommission[0]->value * $deduction) / 100;
            }
            $astrologerCommission = $deduction - $adminCommission;



            // dd($astrologerCommission);



            $astrologerGift=AstrologerGift::create([
                'giftId' => $req->giftId,
                'astrologerId' => $req->astrologerId,
                'userId' => $id,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'giftAmount' => $gift[0]->amount,
            ]);





            $astrologerUserId = DB::table('astrologers')
                ->where('id', '=', $req->astrologerId)
                ->selectRaw('userId,name')
                ->get();
            $userRcd = DB::table('users')
                ->where('id', '=', $id)
                ->selectRaw('id,name')
                ->get();
            $astrologerWallet = DB::table('user_wallets')
                ->where('userId', '=', $astrologerUserId[0]->userId)
                ->get();

            $deduction = $userWallet[0]->amount - $gift[0]->amount;
            $userWalletData = array(
                'amount' => $deduction,
            );
            $astrologerWalletData = array(
                'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                'userId' => $astrologerUserId[0]->userId,
                'createdBy' => $astrologerUserId[0]->userId,
                'modifiedBy' => $astrologerUserId[0]->userId,
            );
            DB::Table('user_wallets')
                ->where('userId', '=', $id)
                ->update($userWalletData);

            if ($astrologerWallet && count($astrologerWallet) > 0) {
                DB::Table('user_wallets')
                    ->where('userId', '=', $astrologerUserId[0]->userId)
                    ->update($astrologerWalletData);
            } else {
                DB::Table('user_wallets')->insert($astrologerWalletData);
            }
            $walletTransaction = array(
                'amount' => $gift[0]->amount,
                'userId' => $id,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isCredit' => false,
                'transactionType' => 'Gift',
                "astrologerId" => $req->astrologerId,
                "createdBy" => $id,
                "modifiedBy" => $id,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
            );
            $astrologerWalletTransaction = array(
                'amount' => $astrologerCommission,
                'userId' => $astrologerUserId[0]->userId,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isCredit' => true,
                'transactionType' => 'Gift',
                "astrologerId" => $req->astrologerId,
                "createdBy" => $id,
                "modifiedBy" => $id,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
            );
            DB::table('wallettransaction')->insert($walletTransaction);
            DB::table('wallettransaction')->insert($astrologerWalletTransaction);


            $orderRequest = array(
                'userId' => $id,
                'astrologerId' => $req->astrologerId,
                'orderType' => 'gift',
                'totalPayable' => $gift[0]->amount,
                'orderStatus' => 'Complete',
                'giftId' => $astrologerGift->id,

            );
           DB::Table('order_request')->insert($orderRequest);
           $orderid = DB::getPdo()->lastInsertId();

            // Commission
            if ($commission && count($commission) > 0 ) {
                $adminGetCommission = array(
                    'commissionTypeId' => 5,
                    "amount" => $adminCommission,
                    "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                    "orderId" => $orderid,
                    "createdBy" => $id,
                    "modifiedBy" => $id,

                );
                DB::table('admin_get_commissions')->insert($adminGetCommission);
            }elseif($syscommission && count($syscommission) > 0){
                $adminGetCommission = array(
                    'commissionTypeId' => 5,
                    "amount" => $adminCommission,
                    "commissionId" => null,
                    "orderId" => $orderid,
                    "createdBy" => $id,
                    "modifiedBy" => $id,
                );
                DB::table('admin_get_commissions')->insert($adminGetCommission);
            }




            DB::commit();

            $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*')
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                $astroName = isset($astrologerUserId[0]->name) ? $astrologerUserId[0]->name : "";
                $userName = isset($userRcd[0]->name) ? $userRcd[0]->name : "";
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Hey '.$astroName.', we have a gift for you',
                        'body' => [
                            "notificationType" => 13,
                            'description' => 'Hey '.$astroName.', you have received one gift from'.$userName,
                            'icon' => 'public/notification-icon/gift.png'
                        ],
                    ]
                );
                $notification = array(
                    'userId' => $astrologerUserId[0]->userId,
                    'title' => 'Receive Gift',
                    'description' => 'Hey '.$astroName.', you have received one gift from'.$userName,
                    'notificationId' => null,
                    'createdBy' => $astrologerUserId[0]->userId,
                    'modifiedBy' => $astrologerUserId[0]->userId,
                    'notification_type' => 0,
                );
                DB::table('user_notifications')->insert($notification);
            }
                $wallet_balance = DB::table('user_wallets')
                ->where('userId', '=', $id)
                ->first();
            return response()->json([
                'recordList' => [],
                'wallet_balance' => $wallet_balance,
                'status' => 200,
                'message' => 'Astrologer Gift Add Successfully',
            ], 200);
        } catch (\Exception$e) {
            DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
