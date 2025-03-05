<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;

class Pet extends Model
{

    use HasFactory;

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
    ];

   public static function factoryFromType(string $type): self
    {
        return match ($type){
            Type::Dog => Dog::factory(),
            Type::Cat => Cat::factory(),
            Type::Bird => Bird::factory(),
           Type::Fish => Fish::factory(),
            Type::Reptile => Reptile::factory(),
            Type::Rabbit => Rabbit::factory(),
            Type::smallMammal => smallMammal::factory(),
            default => User::factory()->type($type),
        };
    }

    public function isType(string $type): bool
    {
    switch ($type) {
        case 'Dog':
            return $this->Type === Type::Dog;
        case 'Cat':
            return $this->Type === Type::Cat;
        case 'Bird':
            return $this->Type === Type::Bird;
        case 'Fish':
            return $this->Type === Type::Fish;
        case 'Reptile':
            return $this->Type === Type::Reptile;
        case 'Rabbit':
            return $this->Type === Type::Rabbit;
        case 'smallMammal':
            return $this->Type === Type::smallMammal;
        default:
            return false;
    }
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'DOB' => 'nullable|integer|min:0',
            'type' => 'required|string|max:255',
            'sex' => 'nullable|string|max:255',
            'species' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:255',
            'neutered' => 'nullable|boolean',
            'color' => 'nullable|string|max:255',
            'weight' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:0',
        ]);

        // Store the new pet
        Pet::store($validated);

        // Redirect back with success message
        return redirect()->route('pets.create')->with('success', 'Pet created successfully!');
    }


    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
