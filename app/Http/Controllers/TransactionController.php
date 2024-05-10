<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {
    public function welcome() {

        return view('welcome');
    }

    public function transactionList() {
        $data = User::find(Auth::id());

        return view('transaction.transaction-list', compact('data'));
    }

    public function depositList() {
        $data = Transaction::where('user_id', Auth::id())->where('transaction_type', 'Deposit')->latest()->get();

        return view('transaction.deposit', compact('data'));
    }

    public function storeDeposit(Request $request) {
        // dd($request->all());
        DB::beginTransaction();
        try {

            $user = User::find(Auth::id());
            $user->balance += $request->amount;
            $user->save();

            Transaction::create([
                'user_id'          => $user->id,
                'transaction_type' => 'Deposit',
                'amount'           => $request->amount,
                'fee'              => 0,
                'date'             => today(),
            ]);

            DB::commit();
            session()->flash('message', 'Balance deposit successfully');

            return back();
        } catch (\Exception $th) {
            DB::rollBack();
            session()->flash('danger', 'Something went wrong');

            return back();
        }

    }

    public function withdrawalList() {
        $data = Transaction::where('user_id', Auth::id())->where('transaction_type', 'Withdrawal')->latest()->get();

        return view('transaction.withdrawal', compact('data'));
    }

    public function storeWithdrawal(Request $request) {

        DB::beginTransaction();
        try {

            $user = User::find(Auth::id());

            if ($user->balance < $request->amount) {
                session()->flash('danger', 'Insufficient amount');

                return back();
            }

            $user->balance -= $request->amount;
            $user->save();

            $fee = 0;

            if ($user->account_type == 'Individual') {

                if (date('D') === 'm') {
                    $fee = 0;
                } else {
                    $remain_amount = $request->amount - 1000;

                    if ($remain_amount > 0) {
                        $fee = ($remain_amount * 0.015) / 100;

                        $tmw = Transaction::where('user_id', Auth::id())
                            ->where('transaction_type', 'Withdrawal')
                            ->whereYear('date', date('Y'))
                            ->whereMonth('date', date('m'))
                            ->sum('amount');

                        if ($tmw <= 5000) {
                            $tmw += $remain_amount;
                            $monthly_fee = $tmw - 5000;

                            if ($monthly_fee <= 0) {
                                $fee = 0;
                            } else {
                                $fee = ($monthly_fee * 0.015) / 100;
                            }

                        }

                    } else {
                        $fee = 0;
                    }

                }

            } else {
                $tmw = Transaction::where('user_id', Auth::id())
                    ->where('transaction_type', 'Withdrawal')
                    ->sum('amount');

                if ($tmw > 50000) {
                    $fee = ($request->amount * 0.015) / 100;
                } else {
                    $total_amount = $tmw + $request->amount;

                    if ($total_amount > 50000) {
                        $low_fee     = $total_amount - 50000;
                        $regular_fee = 50000 - $tmw;

                        $fee = ($low_fee * 0.015) / 100;

                        $fee += ($regular_fee * 0.025) / 100;
                    } else {
                        $fee = ($request->amount * 0.025) / 100;
                    }

                }

            }

            Transaction::create([
                'user_id'          => $user->id,
                'transaction_type' => 'Withdrawal',
                'amount'           => $request->amount,
                'fee'              => $fee,
                'date'             => today(),
            ]);

            DB::commit();
            session()->flash('message', 'Balance withdrawal successfully');

            return back();
        } catch (\Exception $th) {
            DB::rollBack();
            session()->flash('danger', 'Something went wrong');

            return back();
        }

    }

}
