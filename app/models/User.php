<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{

    protected $table = "users";

    protected $fillable = ['user_name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token',];

    // Relationship Posts
    public function posts()
    {
        return $this->hasMany("Post", "user_id");
    }

    // Relationship Comments
    public function comments(){
        return $this->hasMany("Comment");
    }

    public function roles()
    {
        return $this->belongsToMany("Role");
    }

    public function hasOneRoles($roles)
    {
        return $this->roles()->where("title", $roles)->exists();
    }

    public function hasRoles($roles)
    {
        return $this->roles()->whereIn("title", [$roles])->exists();
    }

}