<?php

namespace App\Http\Controllers;

use App\Events\PasswordUpdateEvent;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function me()
    {
        $me = auth()->user();
        return $this->json_success('Profile fetched', $me);
    }

    public function updateProfile(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'firstName' => ['sometimes', 'string', 'max:255'],
            'lastName' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:13', 'min:11'],
            'avatar' => ['sometimes', 'url'],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }

        $me = auth()->user();

        $me->update([
            'firstName' => request('firstName'),
            'lastName' => request('lastName'),
            'phone' => request('phone'),
            'avatar' => request('avatar'),
        ]);

        return $this->json_success('Profile updated', $me);
    }

    public function updatePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'oldPassword' => ['required', new MatchOldPassword],
            'newPassword' => ['required', Rules\Password::defaults()],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }

        $user = auth()->user();

        $user->password = Hash::make(request()->newPassword);

        $user->save();

        event(new PasswordUpdateEvent($user));

        return $this->json_success('Password updated');
    }

    public function uploadImage(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'image' => ['required', 'file'], //or 'image'
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }

        try {
            $image_url = cloudinary()->upload(request()->file('image')->getRealPath(), [
                'folder' => 'uploads',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ])->getSecurePath();
            return $this->json_success('Image uploaded', $image_url);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage(), 500);
        }
    }

    public function addBankDetails(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'bankName' => ['required'],
            'accountName' => ['required'],
            'accountNumber' => ['required', 'size:10'],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }

        try {
            auth()->user()->accountDetail()->update([
                'bankName' => $request->bankName,
                'accountNumber' => $request->accountNumber,
                'accountName' => $request->accountName,
            ]);

            return $this->json_success('Account Detail added');
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage(), 500);
        }
    }

    public function updateAccountDetail(Request $request)
    {
        try {
            $user = auth()->user();
            $user->currentStep = $request->currentStep;
            $user->save();

            $accountDetail = $user->accountDetail;

            $accountDetail->update([
                'userPlan' => $request->plan ? $request->plan : $accountDetail->plan,
                'gender' => $request->gender ? $request->gender : $accountDetail->gender,
                'dateOfBirth' => $request->dateOfBirth ? $request->dateOfBirth : $accountDetail->dateOfBirth,
                'relationshipStatus' => $request->relationshipStatus ? $request->relationshipStatus : $accountDetail->relationshipStatus,
                'address' => $request->address ? $request->address : $accountDetail->address,
                //
                'isHomeOwner' => $request->isHomeOwner ? $request->isHomeOwner : $accountDetail->isHomeOwner,
                'haveTakenMortgage' => $request->haveTakenMortgage ? $request->haveTakenMortgage : $accountDetail->haveTakenMortgage,
                'liveInARentedApartment' => $request->liveInARentedApartment ? $request->liveInARentedApartment : $accountDetail->liveInARentedApartment,
                'contributeToNHF' => $request->contributeToNHF ? $request->contributeToNHF : $accountDetail->contributeToNHF,
                'benefittedFromNHF' => $request->benefittedFromNHF ? $request->benefittedFromNHF : $accountDetail->benefittedFromNHF,
                //
                'numberOfDependant' => $request->numberOfDependant ? $request->numberOfDependant : $accountDetail->numberOfDependant,
                'careerType' => $request->careerType ? $request->careerType : $accountDetail->careerType,
                'occupation' => $request->occupation ? $request->occupation : $accountDetail->occupation,
                'industry' => $request->industry ? $request->industry : $accountDetail->industry,
                'grossAnnualIncome' => $request->grossAnnualIncome ? $request->grossAnnualIncome : $accountDetail->grossAnnualIncome,
                'netMonthlyIncome' => $request->netMonthlyIncome ? $request->netMonthlyIncome : $accountDetail->netMonthlyIncome,
                'incomeSource' => $request->incomeSource ? $request->incomeSource : $accountDetail->incomeSource,
                'employer' => $request->employer ? $request->employer : $accountDetail->employer,
                'jobStatus' => $request->jobStatus ? $request->jobStatus : $accountDetail->jobStatus,
                'employmentStatus' => $request->employmentStatus ? $request->employmentStatus : $accountDetail->employmentStatus,
                //
                'educationalLevel' => $request->educationalLevel ? $request->educationalLevel : $accountDetail->educationalLevel,
                'nextOfKin' => $request->nextOfKin ? $request->nextOfKin : $accountDetail->nextOfKin,
                'nextOfKinRelationship' => $request->nextOfKinRelationship ? $request->nextOfKinRelationship : $accountDetail->nextOfKinRelationship,
                'nextOfKinPhone' => $request->nextOfKinPhone ? $request->nextOfKinPhone : $accountDetail->nextOfKinPhone,
                'emergencyContactName' => $request->emergencyContactName ? $request->emergencyContactName : $accountDetail->emergencyContactName,
                'emergencyContactPhone' => $request->emergencyContactPhone ? $request->emergencyContactPhone : $accountDetail->emergencyContactPhone,
                //
                'passport' => $request->passport ? $request->passport : $accountDetail->passport,
                'validId' => $request->validId ? $request->validId : $accountDetail->validId,
                'utilityBill' => $request->utilityBill ? $request->utilityBill : $accountDetail->utilityBill,
            ]);

            return $this->json_success('Account Detail updated');
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage(), 500);
        }
    }

    public function getBankDetail()
    {
        try {
            $res =   auth()->user()->accountDetail()->latest()->first();

            return $this->json_success('Bank Details Fetched', $res);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage(), 500);
        }
    }
}
