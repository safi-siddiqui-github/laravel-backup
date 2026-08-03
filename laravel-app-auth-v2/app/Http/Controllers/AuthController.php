<?php

namespace App\Http\Controllers;

use App\Enums\Cache\CacheTypeEnum;
use App\Enums\Otp\OtpTypeEnum;
use App\Http\Resources\PersonalAccessToken\PersonalAccessTokenResource;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use ResponseTrait;

    public PersonalAccessTokenController $personalAccessTokenController;
    public UserController $userController;
    public OtpController $otpController;
    public CacheController $cachecontroller;

    public function __construct()
    {
        $this->personalAccessTokenController = new PersonalAccessTokenController();
        $this->userController = new UserController();
        $this->otpController = new OtpController();
        $this->cachecontroller = new CacheController();
    }

    public function emailRegisterRequestOtp(Request $request)
    {
        $this->userController->checkEmailIsUnique();

        request()->mergeIfMissing([
            'type' => OtpTypeEnum::EMAIL_REGISTER_OTP->value,
        ]);
        $this->otpController->store();

        return $this->apiResponse(
            message: 'Otp has been sent to your email',
        );
    }

    public function emailRegisterVerifyOtp(Request $request)
    {
        request()->mergeIfMissing([
            'type' => OtpTypeEnum::EMAIL_REGISTER_OTP->value,
        ]);
        $otp = $this->otpController->verify();

        $token = $this->personalAccessTokenController->storeAllowRegisterUser($otp);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Otp verified',
            data: [
                // 'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function emailRegisterCreateUser(Request $request)
    {
        $user = $this->userController->storeUsingEmailPassword();

        $token = $this->personalAccessTokenController->storeAllowGeneralUser($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'User created',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function passworlessLoginRequestOtp(Request $request)
    {
        $user = $this->userController->verifyUsingEmail();

        request()->mergeIfMissing([
            'type' => OtpTypeEnum::PASSWORDLESS_LOGIN_OTP->value,
            'user_id' => $user->id
        ]);
        $this->otpController->store();

        return $this->apiResponse(
            message: 'Otp has been sent to your email',
        );
    }

    public function passworlessLoginVerifyOtp(Request $request)
    {
        $user = $this->userController->verifyUsingEmail();

        request()->mergeIfMissing([
            'type' => OtpTypeEnum::PASSWORDLESS_LOGIN_OTP->value,
            'user_id' => $user->id
        ]);
        $this->otpController->verify();

        $token = $this->personalAccessTokenController->storeAllowGeneralUser($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'User Logged In',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function emailLogin(Request $request)
    {
        $user = $this->userController->verifyUsingEmailPassword();

        $token = $this->personalAccessTokenController->storeAllowGeneralUser($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'User Logged in',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource
            ]
        );
    }

    /*
    public function verifyEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => "Email already verified",
            ]);
        }

        request()->mergeIfMissing([
            'user_id' => $user->id,
            // 'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
        ]);
        $this->otpController->verify();

        $user->markEmailAsVerified();
        new Verified($user);

        $token = $this->personalAccessTokenController->storeAllowAll($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Email Verified',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => "Email already verified",
            ]);
        }

        request()->mergeIfMissing([
            'user_id' => $user->id,
            // 'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
            'email' => $user->email,
        ]);

        $otp = $this->otpController->store();

        request()->mergeIfMissing([
            'otp_id' => $otp->id,
        ]);
        $this->otpController->expireAll();

        return $this->apiResponse(
            message: 'Email Verification Resend',
        );
    }

    */

    public function socialLogin()
    {
        $user = $this->userController->upsertUsingSocialLogin();

        $token = null;

        if ($user->isEmailVerified()) {
            $token =  $this->personalAccessTokenController->storeAllowAll($user);
        } else {
            // $token =  $this->personalAccessTokenController->storeAllowEmailVerification($user);
        }

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Social Login Success',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function forgotPasswordRequestOtp()
    {
        $user = $this->userController->verifyUsingEmail();

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::PASSWORD_RESET_OTP->value,
        ]);
        $this->otpController->store();

        return $this->apiResponse(
            message: 'Otp has been sent to your email',
        );
    }

    public function forgotPasswordVerifyOtp()
    {
        $user = $this->userController->verifyUsingEmail();
        $user->can_reset_password = true;

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::PASSWORD_RESET_OTP->value,
        ]);
        $this->otpController->verify();

        $token =  $this->personalAccessTokenController->storeAllowForgotPassword($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Otp has been verified',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function forgotPasswordUpdateUser()
    {
        $user = request()->user();

        request()->mergeIfMissing([
            'email' => $user->email,
        ]);

        $this->userController->updatePasswordUsingEmail();

        return $this->apiResponse(
            message: 'Password has been updated successfully',

        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->apiResponse(
            message: 'User has been logged out successfully',
        );
    }
}
