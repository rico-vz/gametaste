<?php 

include __DIR__ . '/vendor/autoload.php';

use RedBeanPHP\R as R;

//connect to database
R::setup('mysql:host=localhost;dbname=gametaste', 'root', '');
R::wipe('users');

    $user = [
        [
        'id' => 1,
        'username' => 'johndoe01',
        'email' => 'johndoe01@coldmail.com',
        'display_name' => 'GamerJohn101',
        'registered' => new DateTime('now'),
        'password' => password_hash('password123', PASSWORD_BCRYPT),
        'security_answer' => 'answer',
        'bio' => null,
        'avatar' => null,
        'twitch' => null,
        'youtube' => null,
        'twitter' => null,
        'instagram' => null,
        'discord' => null
        ],
    ];

    foreach ($user as $usr) {
        $users = R::dispense('users');
        $users->username = $usr['username'];
        $users->email = $usr['email'];
        $users->display_name = $usr['display_name'];
        $users->registered = $usr['registered'];
        $users->password = $usr['password'];
        $users->security_answer = $usr['security_answer'];
        $users->bio = $usr['bio'];
        $users->avatar = $usr['avatar'];
        $users->twitch = $usr['twitch'];
        $users->youtube = $usr['youtube'];
        $users->twitter = $usr['twitter'];
        $users->instagram = $usr['instagram'];
        $users->discord = $usr['discord'];
        
        R::store($users);
    }

R::wipe('reviews');
    $review = [
        [
        'id' => 1,
        'username' => 'johndoe01',
        'game_id' => 1,
        'game_title' => 'Counter Strike: Global Offensive',
        'game_description' => 'Counter-Strike: Global Offensive (CS:GO) is a first-person shooter video game developed by Hidden Path Entertainment and Valve Corporation.',
        'review_title' => 'Title!',
        'review' => 'This is a review for the game',
        'rating' => 5,
        'created_at' => new DateTime('now'),
        'updated_at' => new DateTime('now')
        ],
    ];

    foreach ($review as $rev) {
        $reviews = R::dispense('reviews');
        $reviews->username = $usr['username'];
        $reviews->game_id = $rev['game_id'];
        $reviews->game_title = $rev['game_title'];
        $reviews->game_description = $rev['game_description'];
        $reviews->review_title = $rev['review_title'];
        $reviews->review = $rev['review'];
        $reviews->rating = $rev['rating'];
        $reviews->created_at = $rev['created_at'];
        $reviews->updated_at = $rev['updated_at'];
        
        R::store($reviews);
    }
