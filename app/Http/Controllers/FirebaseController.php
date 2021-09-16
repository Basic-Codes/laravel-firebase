<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    protected $database;

    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    public function replaceInFirebase($user, $shop) {

        $today = Carbon::today();
        $Qs = Queue::where([['user_id', $user->id],['shop_id', $shop->id],['status', '!=', 'skipped'],['status', '!=', 'complete'],['created_at', '>=', $today]])->get();
        
        
        $firebase = (new Factory)
                    ->withServiceAccount(base_path().'/ServiceAccountApiKey.json')
                    ->withDatabaseUri('https://pushnotification-xxxxxxxx-default-rtdb.firebaseio.com');
        
        $database = $firebase->createDatabase();

        $database->getReference(''.$user->id.'/'.$shop->id)->remove();
        
        foreach ($Qs as $Q_i) {
            $newItem = $database->getReference(''.$Q_i->user_id.'/'.$Q_i->shop_id.'/'.$Q_i->status.'')->push([
                'id' => $Q_i->id,
                'phone' => $Q_i->phone,
                'position' => $Q_i->position
            ]);
        }

        return true;

        // print_r($newItem->getvalue());
    }
}
