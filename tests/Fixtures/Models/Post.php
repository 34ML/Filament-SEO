<?php

namespace _34ml\SEO\Tests\Fixtures\Models;

use _34ML\SEO\Traits\SeoTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use SeoTrait;

    protected $fillable = [
        'title',
    ];
}
