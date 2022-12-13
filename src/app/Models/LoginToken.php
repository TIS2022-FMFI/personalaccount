<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoginToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'user_id',
        'valid_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'valid_until' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped, using created_at and updated_at columns.
     * 
     * @var mixed
     */
    public $timestamps = false;

    /**
     * Get the user for whom the token was generated.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the token is valid, i.e. valid_until > now.
     * 
     * @return bool
     */
    public function isValid()
    {
        return $this->valid_until->isAfter(now());
    }

    /**
     * Make the token invalid, so that no future attempts to login
     * using this token will be successful.
     * 
     * @return void
     */
    public function invalidate()
    {
        $this->valid_until = now();
        $this->save();
    }

    /**
     * Generate a new login token for a provided user.
     * 
     * @param User $user
     * @return mixed
     */
    public static function generate(User $user)
    {
        return LoginToken::create([
            'token' => Str::random(32),
            'user_id' => $user->id,
            'valid_until' => now()->addMinutes(15),
        ]);
    }
}
