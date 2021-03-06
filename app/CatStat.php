<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatStat extends Model {
	protected $table = 'cat_stats';
	public $timestamps = false;

	protected $guarded = [];

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