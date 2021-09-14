<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class TestController extends Controller
{
    protected $database;

    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    public function firebaseTest() {
        // $this->database->getReference('title')
        // ->set([
        //     'task' => 'Example Task',
        //     'is_done' => false
        //     ]);
        // return $this->database->getReference('title')->set('New Task Name');
        
        // return base_path().'/ServiceAccountApiKey.json';

        // $serviceAccount = ServiceAccount::fromJsonFile(base_path().'/ServiceAccountApiKey.json');
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
