<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = ['id'];
    protected $appends = [
        'role_label',
        'full_name',
        'total_followers',
        'total_following',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expire_at',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    //attributes
    public function getFirstNameAttribute($value)
    {
        if ($this->role == Role::MEMBER) {
            return $value;
        } elseif ($this->role == Role::STORE) {
            return $this->attributes['store_name'] ?? $value;
        } elseif ($this->role == Role::BRAND) {
            return $this->attributes['brand_name'] ?? $value;
        } else {
            return $this->attributes['name'] ?? $value;
        }
        return $value;
    }


    public function getRoleLabelAttribute(): string
    {
        return $this->role ? Role::from($this->role)->label() : '';
    }

    //full  name
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    //get avatar
    public function getAvatarAttribute($value)
    {
        return $value ? asset('storage/' . $value) : "https://ui-avatars.com/api/?background=random&name={$this->first_name}+{$this->last_name}&bold=true";
    }

    //get total followers
    public function getTotalFollowersAttribute(): int
    {
        return $this->followers()->count();
    }
    //get total following
    public function getTotalFollowingAttribute(): int
    {
        return $this->following()->count();
    }



    //relationships
    public function manageProducts()
    {
        return $this->hasMany(ManageProduct::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')->withTimestamps();
    }

    //forum groups
    public function forumGroups()
    {
        return $this->hasMany(ForumGroup::class);
    }
}
