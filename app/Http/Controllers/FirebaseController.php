<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    protected $database;

    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    public function replaceInFirebase() {
        $firebase = (new Factory)
                    ->withServiceAccount(base_path().'/ServiceAccountApiKey.json')
                    ->withDatabaseUri('https://pushnotification-xxxxxxxx-default-rtdb.firebaseio.com');
        
        $database = $firebase->createDatabase();
        
        $newPost = $database->getReference('blog/posts')->push([
            'title' => 'OOO XXX',
            'body' => 'XOXOXOXOXOXOXOXOXOXOXOXOX.'
        ]);

        print_r($newPost->getvalue());
    }
}
