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



        // ⚽️ Getting all data for this store and converting it to php array
        // $snap = $database->getReference(''.$user->id.'/'.$shop->id);
        // $snap_value = $snap->getValue();
        // foreach ($snap_value as $key => $value) {
        //     $firebase_data[$key] = $value;
        // }
        // return json_encode($firebase_data);
        

        $add_these = [];
        $remove_these = [];
        // ==========================================================================================
        //                                   ⚽️ For Queue list
        // ==========================================================================================
        $queue_snap = $database->getReference(''.$user->id.'/'.$shop->id.'/queue');
        $queue_snap_value = $queue_snap->getValue();
        $firebase_queue = [];
        // converting firebase collection to php array
        if($queue_snap_value) {
            foreach ($queue_snap_value as $key => $value) {
                $value['key'] = $key;
                $firebase_queue[] = $value;
            }
        }
        $firebase_queue_ids = array_column($firebase_queue, 'id');
        
        $sql_queue = $Qs->where('status', 'queue')->toArray();
        $sql_queue_ids = array_column($sql_queue, 'id');

        // Add these queue
        foreach ($sql_queue as $sql_queue_i) {
            if (!in_array($sql_queue_i['id'], $firebase_queue_ids))
                array_push($add_these, $sql_queue_i);
        }
        // Remove these queue
        foreach ($firebase_queue as $firebase_queue_i) {
            if (!in_array($firebase_queue_i['id'], $sql_queue_ids))
                array_push($remove_these, $firebase_queue_i);
        }
        // ==========================================================================================
        //                                   ⚽️ For Serve list
        // ==========================================================================================
        $serve_snap = $database->getReference(''.$user->id.'/'.$shop->id.'/serve');
        $serve_snap_value = $serve_snap->getValue();
        $firebase_serve = [];
        // converting firebase collection to php array
        if($serve_snap_value) {
            foreach ($serve_snap_value as $key => $value) {
                $value['key'] = $key;
                $firebase_serve[] = $value;
            }
        }
        $firebase_serve_ids = array_column($firebase_serve, 'id');
        
        $sql_serve = $Qs->where('status', 'serve')->toArray();
        $sql_serve_ids = array_column($sql_serve, 'id');

        // Add these serve
        foreach ($sql_serve as $sql_serve_i) {
            if (!in_array($sql_serve_i['id'], $firebase_serve_ids))
                array_push($add_these, $sql_serve_i);
        }
        // Remove these serve
        foreach ($firebase_serve as $firebase_serve_i) {
            if (!in_array($firebase_serve_i['id'], $sql_serve_ids))
                array_push($remove_these, $firebase_serve_i);
        }
        // ==========================================================================================
        //                                   ⚽️ For Park list
        // ==========================================================================================
        $park_snap = $database->getReference(''.$user->id.'/'.$shop->id.'/park');
        $park_snap_value = $park_snap->getValue();
        $firebase_park = [];
        // converting firebase collection to php array
        if($park_snap_value) {
            foreach ($park_snap_value as $key => $value) {
                $value['key'] = $key;
                $firebase_park[] = $value;
            }
        }
        $firebase_park_ids = array_column($firebase_park, 'id');
        
        $sql_park = $Qs->where('status', 'park')->toArray();
        $sql_park_ids = array_column($sql_park, 'id');

        // Add these park
        foreach ($sql_park as $sql_park_i) {
            if (!in_array($sql_park_i['id'], $firebase_park_ids))
                array_push($add_these, $sql_park_i);
        }
        // Remove these park
        foreach ($firebase_park as $firebase_park_i) {
            if (!in_array($firebase_park_i['id'], $sql_park_ids))
                array_push($remove_these, $firebase_park_i);
        }
        // ==========================================================================================
        //                                   ⚽️ For Called Item
        // ==========================================================================================
        $called_snap = $database->getReference(''.$user->id.'/'.$shop->id.'/called');
        $called_snap_value = $called_snap->getValue();
        // converting firebase collection to php array
        $firebase_called = [];
        if($called_snap_value) {
            foreach ($called_snap_value as $key => $value) {
                $value['key'] = $key;
                $firebase_called[] = $value;
            }
        }
        $firebase_called = sizeof($firebase_called) > 0 ? $firebase_called[0] : null; // make first in arraw as called
        
        $sql_called = $Qs->where('status', 'called')->first();
        // $sql_called = $sql_calleds->count() > 0 ? $sql_calleds->first() : null;
        if(isset($sql_called) && !isset($firebase_called)) {
            array_push($add_these, $sql_called);
        } else if (!isset($sql_called) && isset($firebase_called)) {
            array_push($remove_these, $firebase_called);
        } else if(isset($sql_called) && isset($firebase_called)) {
            if($sql_called['id'] == $firebase_called['id']) {
                // Do nothing
            } else {
                array_push($add_these, $sql_called);
                array_push($remove_these, $firebase_called);
            }
        }


        
        
        // ==========================================================================================
        //                             Adding $add_these items
        // ==========================================================================================
        foreach ($add_these as $item) {
            $newItem = $database->getReference(''.$item['user_id'].'/'.$item['shop_id'].'/'.$item['status'].'')->push([
                'id' => $item['id'],
                'phone' => $item['phone'],
                'position' => $item['position']
            ]);
        }
        // ==========================================================================================
        //                            Removing $remove_these items
        // ==========================================================================================
        foreach ($remove_these as $item) {
            $database->getReference(''.$user->id.'/'.$shop->id.'/'.'queue/'.$item['key'])->remove();
            $database->getReference(''.$user->id.'/'.$shop->id.'/'.'serve/'.$item['key'])->remove();
            $database->getReference(''.$user->id.'/'.$shop->id.'/'.'park/'.$item['key'])->remove();
            $database->getReference(''.$user->id.'/'.$shop->id.'/'.'called/'.$item['key'])->remove();
        }

        
        return true;
        
        

        // ⚽️ Clear all in shop & replace all
        // $database->getReference(''.$user->id.'/'.$shop->id)->remove();
        // foreach ($Qs as $Q_i) {
        //     $newItem = $database->getReference(''.$Q_i->user_id.'/'.$Q_i->shop_id.'/'.$Q_i->status.'')->push([
        //         'id' => $Q_i->id,
        //         'phone' => $Q_i->phone,
        //         'position' => $Q_i->position
        //     ]);
        // }

        // print_r($newItem->getvalue());
    }
}
