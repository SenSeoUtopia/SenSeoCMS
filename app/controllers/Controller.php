<?php
use Illuminate\Database\Capsule\Manager as Capsule;	
use Illuminate\Container\Container;
use Illuminate\Cache\CacheManager;

class Controller {

	protected $f3;
	protected $cache;
	protected $db;
	protected $base_dir;
	protected $app_dir;
	protected $public_dir;
	protected $home_url;
	protected $template;
	protected $theme;

	function __construct() {

		$f3 = Base::instance();
		$this->f3 = $f3;

		$this->home_url = $f3->get('SCHEME').'://'.$f3->get('HOST').$f3->get('BASE');

		// Base Dir
		$this->base_dir = $f3->get('base_dir');

		// App Dir
		$this->app_dir = $f3->get('app_dir');

		// Public Path
		$this->public_dir = $f3->get('public_dir');

		// Covers Dir
		$this->upload_dir = $f3->get('upload_dir');
		
		// Theme
		$theme = $f3->get('theme');	
		
		if(file_exists($f3->UI."themes/$theme/layout.htm")){
			$template = "themes/$theme/layout.htm";
		} else if(file_exists($this->public_dir."/themes/$theme/layout.htm")) {
			$this->f3->UI = $this->public_dir."/themes/$theme/";
			$template = "layout.htm";
		} else {
			$template = "themes/default/layout.htm";
		}		
		
		$this->theme = $theme;
		
		
		// Theme
		$this->template = $template;		

		// Change Language
		$ip = $f3->get("IP");

		if(!$f3->get("installed")) return $f3->reroute("/install");


		/* Database Connect */
		try {
			$db = new DB\SQL(
			$f3->get('db_dns') . $f3->get('db_name'),
			$f3->get('db_user'),
			$f3->get('db_pass')
			);
		} catch (PDOException $e) {
			$f3->status(503);
			echo "<h1>Error establishing a database connection</h1>";
			exit;
		}

		/* Caching */
		$app = new Container();
		Container::setInstance($app);
		$app->singleton('files', function(){
			return new Illuminate\Filesystem\Filesystem();
		});

		$app->singleton('config', function(){
			return [
			'path.storage' => $this->app_dir.'/cache',
			'cache.default' => 'file',
			'cache.stores.file' => [
			'driver' => 'file',
			'path' => $this->app_dir.'/cache' // bind singleton for path.storage!
			]
			];
		});
		// This is required for the storage_path() function to work properly:
		$app->singleton('path.storage', function(){
			return $this->app_dir.'/cache';
		});


		$cacheManager = new CacheManager($app);
		$cache = $cacheManager->driver();

		$this->cache = $cache;

		/* Database */
		$capsule = new Capsule;

		$capsule->addConnection([
		'driver'    => 'mysql',
		'host'      => $f3->get('db_host','localhost'),
		'database'  => $f3->get('db_name','social'),
		'username'  => $f3->get('db_user','root'),
		'password'  => $f3->get('db_pass',''),
		'charset'   => 'utf8',
		'collation' => 'utf8_general_ci',
		'prefix'    => $f3->get('db_prefix','senseo_')
		]);

		$capsule->setAsGlobal();
		$capsule->bootEloquent();

		try {
			$capsule->connection()->getPdo();
		} catch (Exception $e) {

			$f3->status(503);	

			die("<h1>Error Establishing a Database Connection</h1>");
		}

		// Database
		$this->db = $db;

	}


	// Show Error
	function error($f3){
		$f3->status(404);
		echo Template::instance()->render('error.htm');
		exit;
	}

	// Set Data
	function afterroute($f3) {
		$site_title = $f3->get('site_title');
		$active_menu = $f3->get('PATH');
		$home_url = $this->home_url;
		$meta_keyword = $f3->get('meta_keyword');
		$meta_description = $f3->get('meta_description');
		$recaptcha_key = $f3->get('recaptcha_key');
		$enable_recaptcha = $f3->get('enable_recaptcha');
		$facebook_link = $f3->get('facebook_link');
		$twitter_link = $f3->get('twitter_link');
		$google_plus_link = $f3->get('google_plus_link');
		// Site Title
		$f3->set('site_title',$site_title);
		// Home Url
		$f3->set('home_url',$home_url);
		// Current Page
		$f3->set('active_menu',$active_menu);
		// Meta Keyword
		$f3->set('meta_keyword',$meta_keyword);
		// Meta Description
		$f3->set('meta_description',$meta_description);
		// Recaptcha
		$f3->set('recaptcha_key',$recaptcha_key);
		$f3->set('enable_recaptcha',$enable_recaptcha);
		// Social Menu
		$f3->get('facebook_link',$facebook_link);
		$f3->get('twitter_link',$twitter_link);
		$f3->get('google_plus_link',$google_plus_link);

		$maintenance = $f3->get('maintenance');


		if($maintenance){
			$f3->status(503);

			echo Template::instance()->render('503.htm');
			exit;


		}
		
		/* Register Filters */
		$preview = Template::instance();
		$preview->filter('crop','Helper::instance()->crop');
		$preview->filter('remove_tags','Helper::instance()->remove_tags');
		$preview->filter('remove_slash','Helper::instance()->remove_slash');
		$preview->filter('remove_spaces','Helper::instance()->remove_spaces');
		$preview->filter('remove_execute_code','Helper::instance()->remove_execute_code');
		$preview->filter('remove_white_spaces','Helper::instance()->remove_white_spaces');
		$preview->filter('join','Helper::instance()->join');
		$preview->filter('replace_data','Helper::instance()->replace_data');
		//	echo $preview->render($this->template);
		

		$loader = new Twig_Loader_Filesystem(array($this->f3->get("UI"),$this->f3->UI.'themes/default'));
		$twig = new Twig_Environment($loader, array(
		'cache' => $this->app_dir."/cache",
		'auto_reload' => $f3->get('RELOAD'),
		'debug' => true,
		));

		$twig->addExtension(new Twig_Extension_Debug());

		echo $twig->render($this->template, array('app' => $f3));
	}

}