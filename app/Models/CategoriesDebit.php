<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesDebit extends Model
{
    /**
     * @var string
     */
    protected $table = 'kategori_pemasukan';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'name'
    ];
}
