<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesCredit extends Model
{
    /**
     * @var string
     */
    protected $table = 'kategori_pengeluaran';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'name'
    ];
}
