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

    public const ROLE_OWNER = 'owner';
    public const ROLE_CARETAKER = 'caretaker';

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
    ];

    /**
     * All caretakers (any role) associated with this pet.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pet_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Caretakers with the "owner" role.
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', self::ROLE_OWNER);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owners()->where('users.id', $user->id)->exists();
    }

    public function hasCaretaker(User $user): bool
    {
        return $this->users()->where('users.id', $user->id)->exists();
    }

    protected function casts(): array
    {
        return [
            'DOB' => 'date',
        ];
    }

    public function petInfo()
    {
        return $this->hasOne(PetInfo::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function behaviors()
    {
        return $this->hasMany(Behavior::class);
    }

    public function dailyActivities()
    {
        return $this->hasManyThrough(DailyActivity::class, Activity::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function housing()
    {
        return $this->hasMany(Housing::class);
    }

    public function specialNeeds()
    {
        return $this->hasMany(SpecialNeed::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

}
