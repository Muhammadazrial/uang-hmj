<?php

namespace App\Http\Controllers\api\v1\account;

use App\Models\Credit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaporanCreditController extends Controller
{
    /**
     * LaporanCreditController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'tanggal_awal'     => 'required',
            'tanggal_akhir'    => 'required',

        ],
            [
                'tanggal_awal.required'  => 'Silahkan Pilih Tanggal Awal!',
                'tanggal_akhir.required' => 'Silahkan Pilih Tanggal Akhir!',
            ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'data'    => $validator->errors()
            ],401);

        } else {

            $tanggal_awal  = $request->input('tanggal_awal');
            $tanggal_akhir = $request->input('tanggal_akhir');

            $credit = Credit::select('pengeluaran.id', 'pengeluaran.category_id', 'pengeluaran.user_id', 'pengeluaran.nominal', 'pengeluaran.credit_date', 'pengeluaran.description', 'kategori_pengeluaran.id as id_category', 'kategori_pengeluaran.name')
                ->join('kategori_pengeluaran', 'pengeluaran.category_id', '=', 'kategori_pengeluaran.id', 'LEFT')
                ->whereDate('pengeluaran.credit_date', '>=', $tanggal_awal)
                ->whereDate('pengeluaran.credit_date', '<=', $tanggal_akhir)
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $credit
            ],401);

        }
    }
}
