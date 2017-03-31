<?php

class SearchController extends Controller
{

    // Search Page
    public function search($f3, $args)
    {

        $keyword = $f3->get('GET.s');

        if (isset($keyword)) {

            $title = "Search Results for $keyword";

            $keyword_title = "Search Results for <q>$keyword</q>";

            $home_url = $this->home_url;

            /* Get Series List from Database */

            $limit = 40;
            $page = Pagination::findCurrentPage();

            $subset = Post::where('title', 'like', "%$keyword%")->orWhere('content', 'like', "%$keyword%")->orderBy('title')->paginate($limit, ['*'], 'page', $page);

            $f3->set("post_list", $subset);

            $total_results = $subset->total();
        }

    }
}