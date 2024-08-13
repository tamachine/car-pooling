<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'seats'];

    public function journeys()
    {
        return $this->hasMany(Journey::class);
    }       

    public function scopeWithAvailableSeats($query, $people)
    {
        return $query->whereRaw(
            '(seats - COALESCE((
                SELECT SUM(journey.people)
                FROM journeys AS journey
                WHERE journey.car_id = cars.id
                AND journey.id NOT IN (
                    SELECT journey_id
                    FROM dropoffs
                )
            ), 0)) >= ?',
            [$people]
        );
    }
}


?>