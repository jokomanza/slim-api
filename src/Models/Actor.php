<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{

    /**
     * @var array
     */
    public $fillable = [
        'first_name',
        'surname',
        'email',
        'date_of_birth',
    ];


    public $collums = [
        'id_actor',
        'first_name',
        'last_name',
        'last_update'
    ];

    /**
     * Get actors with given id, if null return all actors
     * 
     * @param int id 
     * 
     * @return array
     */
    public function get($id = null)
    {
        $query = "SELECT * FROM actor " . (!empty($id) ? "WHERE actor_id = $id" : null);
        return Manager::select($query);
    }
}
