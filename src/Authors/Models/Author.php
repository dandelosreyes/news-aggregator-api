<?php

namespace Domain\Authors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'name',
	];
}
