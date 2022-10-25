<?php

use RedBeanPHP\R as R;

class ProfileController extends BaseController
{
    public function index()
    {
        error(404, 'That page does not exist');
        die();
    }

    public function me()
    {
        $this->authorizeUser();

        $username = R::getAll('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $reviews = R::getAll('SELECT * FROM reviews WHERE username = ?', [$username[0]['username']]);
        $avatar = R::getAll('SELECT avatar FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $displayName = R::getAll('SELECT display_name FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $bio = R::getAll('SELECT bio FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $registeredAt = R::getAll('SELECT registered FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitch = R::getAll('SELECT twitch FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $youtube = R::getAll('SELECT youtube FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitter = R::getAll('SELECT twitter FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $instagram = R::getAll('SELECT instagram FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $discord = R::getAll('SELECT discord FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);


        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'avatar' => $avatar[0]['avatar'] ?? 'https://i.imgur.com/0jTi7Te.png',
            'username' => $username[0]['username'],
            'bio' => $bio[0]['bio'] ?? 'This user has not set a bio yet.',
            'twitch' => $twitch[0]['twitch'] ?? null,
            'youtube' => $youtube[0]['youtube'] ?? null,
            'twitter' => $twitter[0]['twitter'] ?? null,
            'instagram' => $instagram[0]['instagram'] ?? null,
            'discord' => $discord[0]['discord'] ?? null,
            'registeredAt' => $registeredAt[0]['registered'],
            'displayName' => $displayName[0]['display_name'],
            'reviews' => $reviews,
        ];
        displayTemplate('profile/me.html', $data);
    }

    public function user()
    {
        if (empty($_GET['name'])) {
            error(404, 'No name given');
            die();
        }

        $temp = R::getAll('SELECT id FROM users WHERE display_name = ?', [$_GET['name']]);
        if (empty($temp)) {
            error(404, 'No user found with name: ' . $_GET['name']);
            die();
        }
        $id = $temp[0]['id'];

        $username = R::getAll('SELECT username FROM users WHERE id = ?', [$id]);
        $reviews = R::getAll('SELECT * FROM reviews WHERE username = ?', [$username[0]['username']]);
        $avatar = R::getAll('SELECT avatar FROM users WHERE id = ?', [$id]);
        $displayName = R::getAll('SELECT display_name FROM users WHERE id = ?', [$id]);
        $bio = R::getAll('SELECT bio FROM users WHERE id = ?', [$id]);
        $registeredAt = R::getAll('SELECT registered FROM users WHERE id = ?', [$id]);
        $twitch = R::getAll('SELECT twitch FROM users WHERE id = ?', [$id]);
        $youtube = R::getAll('SELECT youtube FROM users WHERE id = ?', [$id]);
        $twitter = R::getAll('SELECT twitter FROM users WHERE id = ?', [$id]);
        $instagram = R::getAll('SELECT instagram FROM users WHERE id = ?', [$id]);
        $discord = R::getAll('SELECT discord FROM users WHERE id = ?', [$id]);


        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'avatar' => $avatar[0]['avatar'] ?? 'https://i.imgur.com/0jTi7Te.png',
            'username' => $username[0]['username'],
            'bio' => $bio[0]['bio'] ?? 'This user has not set a bio yet.',
            'twitch' => $twitch[0]['twitch'] ?? null,
            'youtube' => $youtube[0]['youtube'] ?? null,
            'twitter' => $twitter[0]['twitter'] ?? null,
            'instagram' => $instagram[0]['instagram'] ?? null,
            'discord' => $discord[0]['discord'] ?? null,
            'registeredAt' => $registeredAt[0]['registered'],
            'displayName' => $displayName[0]['display_name'],
            'reviews' => $reviews,
        ];

        if ($id == $_SESSION['loggedInUser']) {
            displayTemplate('profile/me.html', $data);
        } else {
            displayTemplate('profile/user.html', $data);
        }
    }

    public function edit()
    {
        $this->authorizeUser();

        $username = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $email = R::getCell('SELECT email FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $display_name = R::getCell('SELECT display_name FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $bio = R::getCell('SELECT bio FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $avatar = R::getCell('SELECT avatar FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitch = R::getCell('SELECT twitch FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $youtube = R::getCell('SELECT youtube FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitter = R::getCell('SELECT twitter FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $instagram = R::getCell('SELECT instagram FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $discord = R::getCell('SELECT discord FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);

        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'error' => null,
            'username' => $username,
            'email' => $email,
            'display_name' => $display_name,
            'bio' => $bio ?? 'This user has not set a bio yet.',
            'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
            'twitch' => $twitch ?? null,
            'youtube' => $youtube ?? null,
            'twitter' => $twitter ?? null,
            'instagram' => $instagram ?? null,
            'discord' => $discord ?? null,
        ];

        displayTemplate('profile/edit.html', $data);
    }

    public function editPost()
    {
        $this->authorizeUser();

        $user = R::dispense('users');
        $user->id = $_SESSION['loggedInUser'];
        $user->username = $_POST['username'];
        $user->display_name = $_POST['display_name'];
        $user->bio = $_POST['bio'];
        $user->avatar = $_POST['avatar'];
        $user->twitch = $_POST['twitch'];
        $user->youtube = $_POST['youtube'];
        $user->twitter = $_POST['twitter'];
        $user->instagram = $_POST['instagram'];
        $user->discord = $_POST['discord'];
        R::store($user);

        $username = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $email = R::getCell('SELECT email FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $display_name = R::getCell('SELECT display_name FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $bio = R::getCell('SELECT bio FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $avatar = R::getCell('SELECT avatar FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitch = R::getCell('SELECT twitch FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $youtube = R::getCell('SELECT youtube FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitter = R::getCell('SELECT twitter FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $instagram = R::getCell('SELECT instagram FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $discord = R::getCell('SELECT discord FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);

        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'error' => 5,
            'username' => $username,
            'email' => $email,
            'display_name' => $display_name,
            'bio' => $bio ?? 'This user has not set a bio yet.',
            'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
            'twitch' => $twitch ?? null,
            'youtube' => $youtube ?? null,
            'twitter' => $twitter ?? null,
            'instagram' => $instagram ?? null,
            'discord' => $discord ?? null,
        ];

        displayTemplate('profile/edit.html', $data);
    }

    public function changePasswordPost()
    {
        $databasePassword = R::getCell('SELECT password FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);

        $username = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $email = R::getCell('SELECT email FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $display_name = R::getCell('SELECT display_name FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $bio = R::getCell('SELECT bio FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $avatar = R::getCell('SELECT avatar FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitch = R::getCell('SELECT twitch FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $youtube = R::getCell('SELECT youtube FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $twitter = R::getCell('SELECT twitter FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $instagram = R::getCell('SELECT instagram FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $discord = R::getCell('SELECT discord FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);

        if (!password_verify($_POST['currentPassword'], $databasePassword)) {
            $data = [
                'error' => 1,
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
                'username' => $username,
                'email' => $email,
                'display_name' => $display_name,
                'bio' => $bio ?? 'This user has not set a bio yet.',
                'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
                'twitch' => $twitch ?? null,
                'youtube' => $youtube ?? null,
                'twitter' => $twitter ?? null,
                'instagram' => $instagram ?? null,
                'discord' => $discord ?? null,
            ];
        } else if ($_POST['newPassword'] != $_POST['newPasswordRepeat']) {
            $data = [
                'error' => 2,
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
                'username' => $username,
                'email' => $email,
                'display_name' => $display_name,
                'bio' => $bio ?? 'This user has not set a bio yet.',
                'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
                'twitch' => $twitch ?? null,
                'youtube' => $youtube ?? null,
                'twitter' => $twitter ?? null,
                'instagram' => $instagram ?? null,
                'discord' => $discord ?? null,
            ];
        } else if (empty($_POST['newPassword'])) {
            $data = [
                'error' => 3,
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
                'username' => $username,
                'email' => $email,
                'display_name' => $display_name,
                'bio' => $bio ?? 'This user has not set a bio yet.',
                'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
                'twitch' => $twitch ?? null,
                'youtube' => $youtube ?? null,
                'twitter' => $twitter ?? null,
                'instagram' => $instagram ?? null,
                'discord' => $discord ?? null,
            ];
        } else {
            $data = [
                'error' => 4,
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
                'username' => $username,
                'email' => $email,
                'display_name' => $display_name,
                'bio' => $bio ?? 'This user has not set a bio yet.',
                'avatar' => $avatar ?? 'https://i.imgur.com/0jTi7Te.png',
                'twitch' => $twitch ?? null,
                'youtube' => $youtube ?? null,
                'twitter' => $twitter ?? null,
                'instagram' => $instagram ?? null,
                'discord' => $discord ?? null,
            ];

            $user = R::dispense('users');
            $user->id = $_SESSION['loggedInUser'];
            $user->username = $username;
            $user->password = password_hash($_POST['newPasswordRepeat'], PASSWORD_BCRYPT);
            R::store($user);
        }

        displayTemplate('profile/edit.html', $data);
    }

    public function profileDelete()
    {


        $this->authorizeUser();
        $userID = $_SESSION['loggedInUser'];
        $user = $this->getBeanById('users', $userID);
        $reviews = R::find('reviews', 'username = ?', [$user->username]);
        R::trashAll($reviews);
        R::trash($user);


        // Delete all reviews by user using "DELETE FROM reviews WHERE username = '$user->username'"



        session_destroy();
        header('Location: /');
        die();
    }
}