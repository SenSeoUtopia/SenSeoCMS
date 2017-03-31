<?php

class LoginController extends Controller
{

    protected $tpl = 'layouts/login.htm';

    function logout($f3)
    {

        $f3->clear('SESSION');

        $f3->clear('logged_in');

        $f3->clear('users');

        $f3->reroute('/');

    }

    public function stop($f3)
    {

        $title = "You don't have sufficient permission to Access this Page. ";

        return $f3->set("page", ["content" => "admin/stop.htm", "title" => $title]);
    }

// Show Login Page

    function login($f3)
    {

        if ($f3->get('SESSION.user')) {
            $f3->reroute('/');
        }

        $title = "Signin";

        $site_key = $f3->get('recaptcha_key');

        $f3->set('page', array('title' => $title, 'content' => 'login.htm', 'site_key' => $site_key));
    }

// Process Login Controller

    function login_process($f3)
    {

        $enable_recaptcha = $f3->get('enable_recaptcha');

        $site_key = $f3->get('recaptcha_key');

        $secret = $f3->get('recaptcha_secret');

        $ip = $f3->get('IP');

        $get_recaptcha_response = $f3->get('POST.g-recaptcha-response');

        $recaptcha = new ReCaptcha\ReCaptcha($secret);

        $resp = $recaptcha->verify($get_recaptcha_response, $ip);

// ReCaptcha
        if ($enable_recaptcha) {
            if (!$resp->isSuccess()) {
                $errors = "Invalid Captcha";
                $title = 'Signin';
                return $f3->set('page', array('title' => $title, 'content' => 'login.htm', 'site_key' => $site_key, 'errors' => $errors));
            }

        } // End of ReCaptcha

        $data = $f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'email' => 'required',
            'password' => 'required'
        ));

        if ($valid === true) {

            $user_name = $data['email'];

            $get_password = $data['password'];

            $user = User::where('email', $user_name)->orWhere('user_name', $user_name)->first();

// Password Verify
            if (isset($user) && password_verify($get_password, $user->password)) {

// Save Session
                if ($f3->set('SESSION.user', $user)) {

                    $user->last_seen($user->id);

                    $f3->reroute('/');

                }

            } else {
                $title = 'Signin';

                $errors = "Invalid Login Please Try Again.";

                $f3->set('page', array('title' => $title, 'content' => 'login.htm', 'site_key' => $site_key, 'error_login' => $errors));
            }


        } else {
            $title = 'Signin';

            $errors = "Fill out all Fields.";

            $f3->set('page', array('title' => $title, 'content' => 'login.htm', 'site_key' => $site_key, 'errors_full' => $valid));
        }


    }


// Show Login Page

    function admin_login($f3)
    {

        $title = "AdminCP Sign in";

        $site_key = $f3->get('recaptcha_key');

        $f3->set('page', array('title' => $title, 'content' => 'admin/login.htm', 'site_key' => $site_key));
    }

// Process Login Controller

    function admin_login_process($f3)
    {

        $enable_recaptcha = $f3->get('enable_recaptcha');

        $site_key = $f3->get('recaptcha_key');

        $secret = $f3->get('recaptcha_secret');

        $ip = $f3->get('IP');

        $get_recaptcha_response = $f3->get('POST.g-recaptcha-response');

        $recaptcha = new ReCaptcha\ReCaptcha($secret);

        $resp = $recaptcha->verify($get_recaptcha_response, $ip);

// ReCaptcha
        if ($enable_recaptcha) {
            if (!$resp->isSuccess()) {
                $errors = "Invalid Captcha";
                $title = 'Signin';
                return $f3->set('page', array('title' => $title, 'content' => 'admin/login.htm', 'site_key' => $site_key, 'errors' => $errors));
            }

        } // End of ReCaptcha

        $data = $f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'email' => 'required',
            'password' => 'required'
        ));

        if ($valid === true) {

            $user_name = $data['email'];

            $get_password = $data['password'];

            $user = User::where('email', $user_name)->orWhere('user_name', $user_name)->first();

// Password Verify
            if (isset($user) && password_verify($get_password, $user->password)) {

// Save Session
                if ($f3->set('SESSION.user', $user)) {

                    $user->last_seen($user->id);

                    $f3->reroute("/admincp");

                }

            } else {
                $title = "AdminCP Sign in";

                $errors = "Invalid Login Please Try Again.";

                $f3->set('page', array('title' => $title, 'content' => 'admin/login.htm', 'site_key' => $site_key, 'inputs' => $data['user_name'], 'errors_login' => $errors));
            } // Invalid User Error

        } else {
            $title = 'Signin';
            $f3->set('page', array('title' => $title, 'content' => 'admin/login.htm', 'site_key' => $site_key, 'inputs' => $data['user_name'], 'errors_full' => $valid));
        }


    }


}