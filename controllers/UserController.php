<?php

session_start();

use RedBeanPHP\R as R;

class UserController extends BaseController
{
    public function index()
    {
        error(404, 'That page does not exist');
        die();
    }

    public function login()
    {
        if (isset($_SESSION['loggedInUser'])) {
            error(304, 'You are already logged in'); // 304 = Not Modified
            die();
        }

        $returnTo = '/';
        if (isset($_GET['returnTo'])) {
            $returnTo = $_GET['returnTo'];
        }

        if (isset($_GET['error'])) {
            $error = $_GET['error'];
        }

        $data = [
            'error' => $error ?? null,
            'returnTo' => $returnTo,
        ];

        displayTemplate('user/login.html', $data);
    }

    public function loginPost()
    {
        $user = R::findOne('users', 'username=?', [$_POST['uname']]);
        if (is_null($user)) {
            header('Location: /user/login?error=user');
            die();
        }
        if (!password_verify($_POST['psw'], $user->password)) {
            header('Location: /user/login?error=password');
            die();
        }
        $_SESSION['loggedInUser'] = $user['id'];
        header("Location: " . $_POST['returnTo']);
        die();
    }

    public function logout()
    {
        session_destroy();
        header("Location: ./login");
        die();
    }

    public function register()
    {
        if (isset($_SESSION['loggedInUser'])) {
            error(304, 'You are already logged in'); // 304 = Not Modified
            die();
        }
        displayTemplate('user/register.html', []);
    }

    public function registerPost()
    {
        $user = R::findOne('users', 'username=?', [$_POST['uname']]);
        $mail = R::findOne('users', 'email=?', [$_POST['email']]);
        if (!is_null($user)) {
            error(401, 'Username already taken'); // 401 = unauthorized
            header('Location: ../user/register');
            die();
        }
        if (!is_null($mail)) {
            error(401, 'Email already taken'); // 401 = unauthorized
            header('Location: ../user/register');
            die();
        }

        $user = R::dispense('users');
        $user->username = $_POST['uname'];
        $user->email = $_POST['email'];
        $user->display_name = $_POST['display_name'];
        $user->registered = new DateTime('now');
        $user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $user->security_answer = $_POST['answer'];
        $user->bio = null;
        $user->avatar = 'https://i.imgur.com/0jTi7Te.png';
        $user->twitch = null;
        $user->youtube = null;
        $user->twitter = null;
        $user->instagram = null;
        $user->discord = null;

        R::store($user);
        header("Location: ../user/login");
        die();
    }

    public function forgotPassword()
    {
        displayTemplate('user/forgotPassword.html', ['error' => null]);
    }

    public function forgotPasswordPost()
    {
        $userInformation = R::getAll('SELECT * FROM users WHERE email = ?', [$_POST['email']]);
        if ($_POST['username'] != $userInformation[0]['username']) {
            displayTemplate('user/forgotPassword.html', ['error' => 1]);
        } else if ($_POST['answer'] != $userInformation[0]['security_answer']) {
            displayTemplate('user/forgotPassword.html', ['error' => 2]);
        } else {
            $data = [
                'username' => $userInformation[0]['username'],
                'email' => $userInformation[0]['email'],
                'display_name' => $userInformation[0]['display_name'],
            ];
            displayTemplate('user/passwordReset.html', $data);
        }
    }

    public function passwordReset()
    {
        $username = R::findOne('users', 'username=?', [$_POST['username']]);

        $user = R::dispense('users');
        $user->id = $username->id;
        $user->username = $username['username'];
        $user->password = password_hash($_POST['passwordRepeat'], PASSWORD_BCRYPT);
        R::store($user);

        displayTemplate('user/login.html', []);
    }
}