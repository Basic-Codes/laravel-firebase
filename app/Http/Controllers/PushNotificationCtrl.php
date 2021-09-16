<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PushNotificationCtrl extends Controller
{
    public function storeToken(Request $request)
    {
        if(!isset($request->curr_user_id)) return ['success'=>false, 'msg'=>'curr_user_id is required'];
        if(!isset($request->token)) return ['success'=>false, 'msg'=>'Token is required'];

        $curr_user = User::find($request->curr_user_id);
        if(!$curr_user) return ['success'=>false, 'msg'=>'Current user not found'];

        if($curr_user->device_key) return ['success'=>false, 'msg'=>'Token already exist'];
        
        $curr_user->device_key = $request->token;
        $curr_user->save();
        
        return ['success'=>true, 'msg'=>'Token successfully stored.'];
    }

    public function sendNotification(Request $request)
    {
        if(!isset($request->user_id)) return ['success'=>false, 'msg'=>'user_id is required'];
        if(!isset($request->title)) return ['success'=>false, 'msg'=>'Title is required'];
        if(!isset($request->body)) return ['success'=>false, 'msg'=>'Body is required'];

        $FcmToken = User::wehre('id', $request->user_id)->pluck('device_key')->get();
        // $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();
        // ⚽️ $FcmToken must be an array
        
        $url = 'https://fcm.googleapis.com/fcm/send';
          
        $serverKey = 'AAAAwr4o7os:APA91bGpDlvfXx9aDNGwWeRibHgtNyc1hb6_h_9-2X3H1RZt9Bxpbqd9gJ2OzlsxMS-0tdU_zuFrsWUMnKMP1-Wm-HxiCeR1nat9Kknn9BdbT9dslYy9m7eLseqoKxOKgg228OrFyDl7';

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response
        return ['success'=>true, 'msg'=>'Token successfully stored.', 'data'=>$result];
    }
}
