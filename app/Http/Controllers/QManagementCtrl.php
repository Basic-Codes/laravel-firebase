<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QManagementCtrl extends Controller
{
    public function addToQ(Request $request) {
        if(!isset($request->phone)) return ['success'=>false, 'msg'=>'Phone number is required'];

        
    }
}
