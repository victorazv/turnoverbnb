<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBankStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request){
        $validatedData = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'unique:users'],
            'password' => ['required'],
        ]);
        if ($validatedData->fails())
        {
            return ["message" => 'Invalid input data'];
        }

        $user = new User();
        $user->fill($request->all());
        $user->password = Hash::make($user->password);

        $user->save();

        return $user;
    }

    public function deposit(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id' => ['required'],
            'amount' => ['required',]
        ]);

        if ($validatedData->fails())
        {
            return ["message" => 'Invalid input data'];
        }

        $deposit = new UserBankStatement();
        $deposit->fill($request->all());
        $deposit->picture = Storage::disk('local')->put('deposits', $request->picture);

        $deposit->save();

        return $deposit;
    }

    public function buy(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id' => ['required'],
            'amount' => ['required',]
        ]);

        if ($validatedData->fails()){
            return ["message" => 'Invalid input data'];
        }

        $user_money = UserBankStatement::where('user_id', $request->user_id)->where('check_approved', true)->sum('amount');

        if ($user_money >= $request->amount){
            $deposit = new UserBankStatement();
            $deposit->fill($request->all());
            $deposit->amount = $deposit->amount * -1;
            $deposit->save();

            return $deposit;
        }else{
            return ["message" => 'Insufficient funds'];
        }
    }

    public function getBalance(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id' => ['required'],
        ]);

        if ($validatedData->fails()){
            return ["message" => 'Invalid input data'];
        }

        return UserBankStatement::where('user_id', $request->user_id)->where('check_approved', true)->get();
    }

    public function getPendingChecks(Request $request){
        return UserBankStatement::where('check_approved', false)->get();
    }
    public function approveDeposit(Request $request){
        $deposit = UserBankStatement::find($request->id);

        if ($deposit) {
            if ($deposit->check_approved) {
                return ["message" => "Check already approved"];
            } elseif ($deposit->amount < 0) {
                return ["message" => "Error: Its a buy data"];
            }

            $deposit->check_approved = true;
            $deposit->save();

            return $deposit;
        }
        return ["message" => "Deposit not found"];
    }
}
