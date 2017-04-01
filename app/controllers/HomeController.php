<?php
class HomeController extends Controller{

	// Show Posts
	public function home($app,$args){

		$title = $app->get("site_title");

        $app->set("page",["content" => "home.htm","title" => $title]);

	}


	// Show Single Post
	public function show_single_post($app,$args){

	}
	

}