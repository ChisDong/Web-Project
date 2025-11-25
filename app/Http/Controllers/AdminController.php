<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showManager(){
        return view('managers.admin_manager');
    }

    public function showIndex(){
        $users = User::orderBy('id', 'desc')->paginate(15);

        // Prepare last 12 months labels and counts
        $months = [];
        $counts = [];
        $now = Carbon::now()->startOfMonth();

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $label = $date->format('Y-m');
            $months[] = $label;
            $counts[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return view('managers.index', compact('users', 'months', 'counts'));
    }

    public function deleteUser($id){
        $user = User::findOrFail($id);

        // Prevent deleting admin accounts
        if ($user->role === 'admin') {
            return redirect()->route('showIndex')->with('error', 'Không thể xóa tài khoản admin.');
        }

        // Prevent deleting yourself
        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->route('showIndex')->with('error', 'Bạn không thể xóa chính mình.');
        }

        $user->delete();
        return redirect()->route('showIndex')->with('message', 'Xóa người dùng thành công!');
    }

    // statistics are prepared in showIndex

}
