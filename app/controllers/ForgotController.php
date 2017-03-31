<?php

class ForgotController extends Controller
{

    protected $tpl = "layouts/login.htm";

    function forgot($f3)
    {

        $title = "Forgot Password";

        $site_key = $f3->get('recaptcha_key');

        $f3->set('page', array('title' => $title, 'content' => 'forgot.htm', 'site_key' => $site_key));
    }

    function forgot_process($f3)
    {

        $secret = $f3->get('recaptcha_secret');

        $ip = $f3->get('IP');

        $data = $f3->get('POST');

        $get_recaptcha_response = $data['g-recaptcha-response'];

        $recaptcha = new ReCaptcha\ReCaptcha($secret);

        $resp = $recaptcha->verify($get_recaptcha_response, $ip);
        if ($resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            $site_key = $f3->get('recaptcha_key');
            $title = "Forgot Password";
            return $f3->set('page', array('title' => $title, 'content' => 'forgot.htm', 'site_key' => $site_key, 'errors' => $errors));
        }

        $email = $data['email'];

        $token = hash_hmac('sha256', Str::random(40));

        dd($token);

        /*
        public function uniqueToken() {
                $token	= bin2hex(openssl_random_pseudo_bytes(20));
                $check	= $this->where('token', '=', $token)->first();
                if($check === NULL) {
                    return $token;
                }else{
                    return $this->uniqueToken();
                }
            }
        */

    }
}