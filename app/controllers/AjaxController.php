<?php

class AjaxController extends Controller
{

    protected $tpl = null;

    // Ajax Check Series Exists
    public function ajax_check_post($f3)
    {

        $post_slug = $f3->get("POST.post_slug");

        if (Post::where("slug", $post_slug)->exists()) {
            $msg = array("success" => true);
        } else {
            $msg = array("success" => false);
        }

        return Response::json($msg);
    }


}