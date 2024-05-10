<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Authcontroller extends Controller {
    public function register() {
        return view('auth.register');
    }

    public function storeRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'         => 'required',
            'email'        => 'required|unique:users|email',
            'password'     => 'required|min:8',
            'account_type' => 'required',
            'balance'      => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => bcrypt($request->password),
                'account_type' => $request->account_type,
                'balance'      => $request->balance,
            ]);

            Transaction::create([
                'user_id'          => $user->id,
                'transaction_type' => 'Deposit',
                'amount'           => $user->balance,
                'fee'              => 0,
                'date'             => today(),
            ]);

            DB::commit();
            session()->flash('message', 'User created successfully');

            return to_route('login');
        } catch (Exception $th) {
            DB::rollBack();
            session()->flash('message', 'Something went wrong');

            return back();
        }

    }

    public function login() {
        return view('auth.login');
    }

    public function storeLogin(Request $request) {

        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (
            Auth::attempt([
                'email'    => $request->email,
                'password' => $request->password,
            ])
        ) {

            return to_route('welcome');

        }

        session()->flash('message', 'Invalid Credentitials!!');

        return to_route('login');

    }

}
