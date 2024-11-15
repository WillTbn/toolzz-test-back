<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Chat extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'hash_id',
    ];
    public function latestMessage():HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
    public function chatMessages():HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
    public function scopeOtherUser($query, $loggedInUserId, $hash_id)
    {
        return $query
        ->where('chats.hash_id', $hash_id)
        ->select(
            DB::raw("IF(user_one_id = $loggedInUserId, user_two_id, user_one_id) as other_user_id")
        )
        ->join('users as other_user', DB::raw("IF(chats.user_one_id = $loggedInUserId, chats.user_two_id, chats.user_one_id)"), '=', 'other_user.id')
            ->where(function($query) use ($loggedInUserId) {
                $query->where('chats.user_one_id', $loggedInUserId)
                    ->orWhere('chats.user_two_id', $loggedInUserId);
            })
            ->select(
            'chats.hash_id',
            'other_user.id as other_user_id',
            'other_user.name as other_user_name',
            // DB::raw("CONCAT('".env('APP_URL_C')."/', other_user.photo) as other_user_photo"),
            DB::raw("CONCAT(' ".asset('storage/')."/', other_user.photo) as other_user_photo"),
        );
    }
}
