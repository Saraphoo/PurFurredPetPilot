<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $DOB
 * @property string $type
 * @property string|null $species
 * @property string|null $breed
 * @property bool|null $neutered
 * @property string|null $color
 * @property string|null $weight
 * @property string|null $height
 * @property string|null $length
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */
class Pet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'DOB',
        'type',
        'species',
        'sex',
        'breed',
        'neutered',
        'color',
        'weight',
        'height',
        'length',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pet_user');
    }
    protected function casts(): array
    {
        return [
            'DOB' => 'date',
        ];
    }

    // In your Pet model
    public function petInfo()
    {
        return $this->hasOne(PetInfo::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function dailyActivities()
    {
        return $this->hasManyThrough(DailyActivity::class, Activity::class);
    }

}
