<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QManagementCtrl extends Controller
{
    public function addToQ(Request $request) {
        if(!isset($request->phone)) return ['success'=>false, 'msg'=>'Phone number is required'];
        if(!isset($request->curr_user_id)) return ['success'=>false, 'msg'=>'curr_user_id is required'];
        if(!isset($request->type)) return ['success'=>false, 'msg'=>'Type is required'];

        $curr_user = User::find($request->curr_user_id);
        if(!$curr_user) return ['success'=>false, 'msg'=>'Current user not found'];
        if($curr_user->active_shop) $curr_shop = Shop::find($curr_user->active_shop);
        else $curr_shop = Shop::where('user_id', $curr_user->id)->first();
        if(!$curr_shop) return ['success'=>false, 'msg'=>'Shop not found'];

        $today = Carbon::today();
        $last_Q = Queue::where([
                ['user_id', $curr_user->id],
                ['shop_id', $curr_shop->id],
                ['status', '!=', 'complete'],
                ['created_at', '>=', $today]
            ])->latest('created_at')->first();
        $next_pos = $this->countNextPos($last_Q);
        
        
        // Adding Queue
        $Q = new Queue();
        $Q->user_id = $curr_user->id;
        $Q->shop_id = $curr_shop->id;
        $Q->phone = $request->phone;
        $Q->position = $next_pos;
        $Q->status = 'queue';
        $Q->save();
        $msg = "Queue Added";

        if($request->type == 'serve') {
            $Q->status = 'serve';
            $Q->save();
            $msg = "Queue Added to serving list";
        }
        if($request->type == 'park') {
            $Q->status = 'park';
            $Q->save();
            $msg = "Queue Added parked list";
        }
        return ['success'=>true, 'msg'=>$msg];
    }

    protected function countNextPos($last_Q) {
        $last_pos = isset($last_Q) ? $last_Q->position : '0000';
        $next_pos = intval($last_pos) + 1;
        switch (strlen((string)$next_pos)) {
            case 1:
                $next_pos = '0000'.$next_pos;
                break;
            case 2:
                $next_pos = '000'.$next_pos;
                break;
            case 3:
                $next_pos = '000'.$next_pos;
                break;
            default:
                $next_pos = '0000'.$next_pos;
                break;
        }
        return $next_pos;
    }
}
