<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request) {
        if(!isset($request->user_id)) return "Please add an '?user_id=1' as params";
        
        $users = User::all();

        $curr_user = User::find($request->user_id);
        if(!$curr_user && $users->count() > 0) return 'Current user not found';

        $shops = [];
        $curr_shop = '';
        if($curr_user) {
            $shops = Shop::where('user_id', $curr_user->id)->get();
            if($curr_user->active_shop) $curr_shop = Shop::find($curr_user->active_shop);
            else $curr_shop = Shop::where('user_id', $curr_user->id)->first();
        }
        
        // ⚽️ Might show some bug if $curr_user or $curr_shop doesn't exist
        if($curr_user && $curr_shop) (new FirebaseController())->replaceInFirebase($curr_user, $curr_shop);
        
        return view('index', compact('users', 'curr_user', 'shops', 'curr_shop'));
    }

    public function addUser(Request $request) {
        if(!isset($request->name)) return "Name is required";
        
        $user = new User();
        $user->name = $request->name;
        $user->save();
        return back();
    }
    public function addShop(Request $request) {
        if(!isset($request->name)) return "Name is required";
        if(!isset($request->user_id)) return "user_id not found";

        $curr_user = User::find($request->user_id);
        if(!$curr_user) return 'Current user not found';

        $shop = new Shop();
        $shop->user_id = $curr_user->id;
        $shop->name = $request->name.' ('.$curr_user->name.')';
        $shop->save();
        return back();
    }

    public function activeShop(Request $request) {
        if(!isset($request->shop_id)) return "Shop Id is required";

        $shop = Shop::find($request->shop_id);
        if(!$shop) return 'Shop not found';
        $user = User::find($shop->user_id);
        if(!$user) return 'User not found';

        $user->active_shop = $shop->id;
        $user->save();
        return back();
    }
}
