<?php

namespace App\Console\Commands;

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

	    $$this->client = new GuzzleHttp\Client();


	    $day    = date('d')-1;
	    $year   = date('Y');
	    $day2   = $day + 1;
	    $month = $month2 = date('m');
	    $year2  = date('Y');
	    if ( $day2 > cal_days_in_month(CAL_GREGORIAN, $month, $year) ) {
		    $day2 = 1;
		    $month2 ++;
		    if ($month2 == 13) {
		        $month2 = 1;
		        $year2++;
		    }
	    }
	    
	    $this->pull($day, $month, $year, $day2, $month2, $year2);
	    return true;
    }

    private function pull($day, $month, $year, $day2, $month2, $year2) {
	    $s      = true;
	    $data   = [];
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
					    if ( isset( $data[ $post['author']['login'] ] ) ) {
						    $data[ $post['author']['login'] ]['post_count'] ++;
						    $data[ $post['author']['login'] ]['img_count']  += substr_count( $post['content'], 'img' );
						    $data[ $post['author']['login'] ]['char_count'] += mb_strlen( str_replace( ' ', '', strip_tags( $post['content'] ) ) );
					    } else {
						    $data[ $post['author']['login'] ] = [
							    'name'       => $post['author']['name'],
							    'email'      => $post['author']['email'],
							    'post_count' => 1,
							    'img_count'  => substr_count( $post['content'], 'img' ),
							    'char_count' => mb_strlen( strip_tags( $post['content'] ) )
						    ];
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
    }
}
