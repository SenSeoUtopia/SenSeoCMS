<?php

class RssController extends Controller
{

// Rss Feed
    public function rss_feed($f3)
    {

        $home_url = $this->home_url;

        $base_dir = $this->base_dir;

        $rss_title = $f3->get('site_title');

    }
}