<?php

namespace App\Http\Controllers\Api\V2;

use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequest;
use Illuminate\Support\Str;
use App\Http\Controllers\OTPVerificationController;
use App\Mail\SecondEmailVerifyMailManager;
use App\Utility\SmsUtility;
use Mail;

use Hash;

class PasswordResetController extends Controller
{
    public function forgetRequest(Request $request)
    {
        if ($request->send_code_by == 'email') {
            $user = User::where('email', $request->email_or_phone)->first();
        } else {
            $user = User::where('phone', $request->email_or_phone)->first();
        }


        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User is not found')], 200);
        }

        if ($user) {
            $user->verification_code = rand(100000, 999999);
            $user->save();
            if ($request->send_code_by == 'phone') {

                $array['view'] = 'emails.verification';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = translate('Email Verification');
                $array['content'] = translate('Verification Code is ').$user->verification_code;

                Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
                // $otpController = new OTPVerificationController();
                // $otpController->send_code($user);
            } else {
                $user->notify(new AppEmailVerificationNotification());
            }
        }

        return response()->json([
            'result' => true,
            'message' => translate('A code is sent')
        ], 200);
    }

    public function confirmReset(Request $request)
    {
        $user = User::where('verification_code', $request->verification_code)->first();

        if ($user != null) {
            $user->verification_code = null;
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your password is reset.Please login'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Wrong code'),
            ], 200);
        }
    }

    public function resendCode(Request $request)
    {

        if ($request->verify_by == 'email') {
            $user = User::where('email', $request->email_or_phone)->first();
        } else {
            $user = User::where('phone', $request->email_or_phone)->first();
        }


        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User is not found')], 200);
        }

        $user->verification_code = rand(100000, 999999);
        $user->save();

        if ($request->verify_by == 'email') {
            $user->notify(new AppEmailVerificationNotification());
        } else {
            
            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Email Verification');
            $array['content'] = translate('Verification Code is ').$user->verification_code;

            Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
            // $otpController = new OTPVerificationController();
            // $otpController->send_code($user);
        }



        return response()->json([
            'result' => true,
            'message' => translate('A code is sent again'),
        ], 200);
    }
}
