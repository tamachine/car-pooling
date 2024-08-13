<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    use HasFactory;
    
    protected $fillable = ['id', 'people', 'car_id'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function dropoff()
    {
        return $this->hasOne(Dropoff::class, 'journey_id');
    }

    /**
     * Scope a query to only include journeys that do not have a dropoff.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutDropoff($query)
    {
        return $query->whereDoesntHave('dropoff');
    }

    /**
     * Scope a query to only include journeys that do not have a car assigned.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutCar($query)
    {
        return $query->whereNull('car_id');
    }

     /**
     * Scope a query to only include journeys created before a given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \DateTime|string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBefore($query, $date)
    {
        return $query->where('created_at', '<', $date);
    }

}



?>