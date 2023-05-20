<?php

namespace App\Http\Controllers\api\v1\account;

use App\Models\Credit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    /**
     * CreditController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $credit = DB::table('pengeluaran')
            ->select('pengeluaran.id', 'pengeluaran.category_id', 'pengeluaran.user_id', 'pengeluaran.nominal', 'pengeluaran.credit_date', 'pengeluaran.description', 'kategori_pengeluaran.id as id_category', 'kategori_pengeluaran.name as category_name')
            ->join('kategori_pengeluaran', 'pengeluaran.category_id', '=', 'kategori_pengeluaran.id', 'LEFT')
            ->where('pengeluaran.user_id', Auth::user()->id)
            ->orderBy('pengeluaran.created_at', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $credit
        ],200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'nominal'        => 'required',
            'credit_date'    => 'required',
            'category_id'    => 'required',
            'description'    => 'required',

        ],
            [
                'nominal.required' => 'Masukkan Nominal Debit / Uang Masuk!',
                'credit_date.required' => 'Silahkan Pilih Tanggal!',
                'category_id.required' => 'Silahkan Pilih Kategori!',
                'description.required' => 'Masukkan Keterangan!',
            ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'data'    => $validator->errors()
            ],401);

        } else {

            Credit::create([
                'user_id'       => Auth::user()->id,
                'credit_date'   => $request->input('debit_date'),
                'category_id'   => $request->input('category_id'),
                'nominal'       => str_replace(",", "", $request->input('nominal')),
                'description'   => $request->input('description'),
            ]);

            return response()->json([
                'success' => true,
                'data'    => 'Data Berhasil Disimpan !'
            ],200);

        }
    }

}
