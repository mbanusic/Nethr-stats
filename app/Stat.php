<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model {
	protected $table = 'stats';
	public $timestamps = false;

	protected $fillable = ['posts', 'chars', 'images', 'day', 'month', 'year',  'user_id'];

	public function user() {
		return $this->belongsTo('App\User');
	}

	public function scopeOfDay($query, $day) {
		return $query->where('day', $day);
	}

	public function scopeOfMonth($query, $month) {
		return $query->where('month', $month);
	}

	public function scopeOfYear($query, $year) {
		return $query->where('year', $year);
	}
}