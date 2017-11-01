<?php

namespace App\Console\Commands;

use App\CatStat;
use App\Stat;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp;

class PullCommand extends Command
{

	private $auth = '';
	private $blog_id = '';
	private $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nethr:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull articles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->auth = env('WP_AUTH');
        $this->blog_id = env('WP_BLOG');

	    $this->client = new GuzzleHttp\Client();

	    $day   = date('d', strtotime('yesterday'));
	    $month = date('m', strtotime('yesterday'));
	    $year  = date('Y', strtotime('yesterday'));

	    $this->pull_day($day, $month, $year);

	    return true;
    }

    private function pullOld() {
    	$months = [1,2,3,4,5];
    	foreach ($months as $month) {
			for($i=1; $i<=cal_days_in_month(CAL_GREGORIAN, $month, 2017); $i++) {
				if ($month == 5 && $i>9) {
					continue;
				}
				$this->pull_day($i, $month, 2017);
			}
	    }
    }

    public function pullOldCategory($day, $month, $year) {
	    $month2 = $month;
	    $day2   = $day + 1;
	    $year2  = $year;
	    if ( $day2 > cal_days_in_month(CAL_GREGORIAN, $month, $year) ) {
		    $day2 = 1;
		    $month2 ++;
		    if ($month2 == 13) {
			    $month2 = 1;
			    $year2++;
		    }
	    }
	    $s      = true;
	    $cats   = [
		    'danas' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'sport' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'hot'   => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'magazin' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'zena' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'auto' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
		    'webcafe' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    ];
	    $offset = 0;
	    while ( $s ) {
		    $res    = $this->client->request( 'GET', 'https://public-api.wordpress.com/rest/v1.1/sites/' . $this->blog_id . '/posts/', [
			    'headers' => [
				    'Authorization' => 'Bearer ' . $this->auth
			    ],
			    'query'   => [
				    'context' => 'edit',
				    'type'    => 'any',
				    'number'  => 20,
				    'offset'  => $offset,
				    'after'   => Carbon::create( $year, $month, $day, 1, 0, 0, 'Europe/Zagreb' )->toIso8601String(),
				    'before'  => Carbon::create( $year2, $month2, $day2, 1, 0, 0, 'Europe/Zagreb' )->toIso8601String()
			    ]
		    ] );
		    $status = $res->getStatusCode();
		    if ( 200 == $status ) {
			    $body = json_decode( $res->getBody(), true );
			    if ( count( $body['posts'] ) ) {
				    foreach ( $body['posts'] as $post ) {
					    if ( 'attachment' == $post['type'] ) {
						    continue;
					    }
					    if ( ! mb_strlen( $post['content'] ) ) {
						    continue;
					    }
					    $content = strip_tags( $post['content'] );
					    $content = str_replace(' ', '', $content);
					    $content = str_replace("\r", '', $content);
					    $content = str_replace("\n", '', $content);
					    $url = $post['URL'];
					    if (strpos($url, 'net.hr/danas')>-1 || strpos($url, 'net.hr/vijesti')>-1) {
						    $cats['danas']['posts']++;
						    $cats['danas']['chars'] += mb_strlen( $content );
						    $cats['danas']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/sport')>-1) {
						    $cats['sport']['posts']++;
						    $cats['sport']['chars'] += mb_strlen( $content );
						    $cats['sport']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/zena')>-1) {
						    $cats['zena']['posts']++;
						    $cats['zena']['chars'] += mb_strlen( $content );
						    $cats['zena']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/hot')>-1) {
						    $cats['hot']['posts']++;
						    $cats['hot']['chars'] += mb_strlen( $content );
						    $cats['hot']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/magazin')>-1) {
						    $cats['magazin']['posts']++;
						    $cats['magazin']['chars'] += mb_strlen( $content );
						    $cats['magazin']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/auto')>-1) {
						    $cats['auto']['posts']++;
						    $cats['auto']['chars'] += mb_strlen( $content );
						    $cats['auto']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/webcafe')>-1) {
						    $cats['webcafe']['posts']++;
						    $cats['webcafe']['chars'] += mb_strlen( $content );
						    $cats['webcafe']['images'] += substr_count( $post['content'], 'img' );
					    }
				    }
			    } else {
				    $s = false;
			    }
		    } else {
			    $s = false;
		    }
		    $offset += 20;
	    }
	    foreach ($cats as $cat => $data) {
		    $c = CatStat::create([
			    'chars'   => $data['chars'],
			    'posts'   => $data['posts'],
			    'images'  => $data['images'],
			    'day'     => $day,
			    'month'   => $month,
			    'year'    => $year,
			    'category' => $cat
		    ]);
	    }
	}

    private function pull_day($day, $month, $year) {
	    $month2 = $month;
	    $day2   = $day + 1;
	    $year2  = $year;
	    if ( $day2 > cal_days_in_month(CAL_GREGORIAN, $month, $year) ) {
		    $day2 = 1;
		    $month2 ++;
		    if ($month2 == 13) {
			    $month2 = 1;
			    $year2++;
		    }
	    }
	    $s      = true;
	    $data   = [];
	    $cats   = [
	    	'danas' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'sport' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'hot'   => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'magazin' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'zena' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'auto' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    	'webcafe' => [ 'chars' => 0, 'posts' => 0, 'images' => 0 ],
	    ];
	    $offset = 0;
	    while ( $s ) {
		    $res    = $this->client->request( 'GET', 'https://public-api.wordpress.com/rest/v1.1/sites/' . $this->blog_id . '/posts/', [
			    'headers' => [
				    'Authorization' => 'Bearer ' . $this->auth
			    ],
			    'query'   => [
				    'context' => 'edit',
				    'type'    => 'any',
				    'number'  => 20,
				    'offset'  => $offset,
				    'after'   => Carbon::create( $year, $month, $day, 1, 0, 0, 'Europe/Zagreb' )->toIso8601String(),
				    'before'  => Carbon::create( $year2, $month2, $day2, 1, 0, 0, 'Europe/Zagreb' )->toIso8601String()
			    ]
		    ] );
		    $status = $res->getStatusCode();
		    if ( 200 == $status ) {
			    $body = json_decode( $res->getBody(), true );
			    if ( count( $body['posts'] ) ) {
				    foreach ( $body['posts'] as $post ) {
					    if ( 'attachment' == $post['type'] ) {
						    continue;
					    }
					    if ( ! mb_strlen( $post['content'] ) ) {
						    continue;
					    }
					    $content = strip_tags( $post['content'] );
					    $content = str_replace(' ', '', $content);
					    $content = str_replace("\r", '', $content);
					    $content = str_replace("\n", '', $content);
					    if ( isset( $data[ $post['author']['login'] ] ) ) {
						    $data[ $post['author']['login'] ]['post_count'] ++;
						    $data[ $post['author']['login'] ]['img_count']  += substr_count( $post['content'], 'img' );
						    $data[ $post['author']['login'] ]['char_count'] += mb_strlen( $content );
					    } else {
						    $data[ $post['author']['login'] ] = [
							    'name'       => $post['author']['name'],
							    'email'      => $post['author']['email'],
							    'post_count' => 1,
							    'img_count'  => substr_count( $post['content'], 'img' ),
							    'char_count' => mb_strlen( $content )
						    ];
					    }
					    $url = $post['URL'];
					    if (strpos($url, 'net.hr/danas')>-1 || strpos($url, 'net.hr/vijesti')>-1) {
					    	$cats['danas']['posts']++;
					    	$cats['danas']['chars'] += mb_strlen( $content );
					    	$cats['danas']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/sport')>-1) {
						    $cats['sport']['posts']++;
						    $cats['sport']['chars'] += mb_strlen( $content );
						    $cats['sport']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/zena')>-1) {
						    $cats['zena']['posts']++;
						    $cats['zena']['chars'] += mb_strlen( $content );
						    $cats['zena']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/hot')>-1) {
						    $cats['hot']['posts']++;
						    $cats['hot']['chars'] += mb_strlen( $content );
						    $cats['hot']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/magazin')>-1) {
						    $cats['magazin']['posts']++;
						    $cats['magazin']['chars'] += mb_strlen( $content );
						    $cats['magazin']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/auto')>-1) {
						    $cats['auto']['posts']++;
						    $cats['auto']['chars'] += mb_strlen( $content );
						    $cats['auto']['images'] += substr_count( $post['content'], 'img' );
					    }
					    if (strpos($url, 'net.hr/webcafe')>-1) {
						    $cats['webcafe']['posts']++;
						    $cats['webcafe']['chars'] += mb_strlen( $content );
						    $cats['webcafe']['images'] += substr_count( $post['content'], 'img' );
					    }
				    }
			    } else {
				    $s = false;
			    }
		    } else {
			    $s = false;
		    }
		    $offset += 20;
	    }

	    foreach ( $data as $login => $one ) {
		    $this->info( $login . ' - ' . $one['post_count'] . ' - ' . $one['img_count'] . ' - ' . $one['char_count'] );
		    $user = User::firstOrCreate(
			    [ 'login' => $login ],
			    [ 'name'     => $one['name'],
			      'email'    => $one['email'],
			      'password' => bcrypt( $one['email'] )
			    ]
		    );
		    $stat = Stat::create( [
			    'chars'   => $one['char_count'],
			    'posts'   => $one['post_count'],
			    'images'  => $one['img_count'],
			    'day'     => $day,
			    'month'   => $month,
			    'year'    => $year,
			    'user_id' => $user->id
		    ] );
	    }
	    foreach ($cats as $cat => $data) {
	    	$c = CatStat::create([
			    'chars'   => $data['chars'],
			    'posts'   => $data['posts'],
			    'images'  => $data['images'],
			    'day'     => $day,
			    'month'   => $month,
			    'year'    => $year,
			    'category' => $cat
		    ]);
	    }
    }
}
