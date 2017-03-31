<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{

    protected $table = 'post';

// Relationship
    public function user()
    {
        return $this->belongsTo("User");
    }

    public function comment()
    {
        return $this->hasMany("Comment");
    }

}