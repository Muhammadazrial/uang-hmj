<?php

namespace App\Http\Controllers\account;

use App\Models\Credit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanCreditController extends Controller
{
    /**
     * LaporanCreditController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('account.laporan_credit.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function check(Request $request)
    {
        //set validasi required
        $this->validate($request, [
            'tanggal_awal'     => 'required',
            'tanggal_akhir'    => 'required',
        ],
            //set message validation
            [
                'tanggal_awal.required'  => 'Silahkan Pilih Tanggal Awal!',
                'tanggal_akhir.required' => 'Silahkan Pilih Tanggal Akhir!',
            ]
        );

        $tanggal_awal  = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        $credit = Credit::select('pengeluaran.id', 'pengeluaran.category_id', 'pengeluaran.user_id', 'pengeluaran.nominal', 'pengeluaran.credit_date', 'pengeluaran.description', 'kategori_pengeluaran.id as id_category', 'kategori_pengeluaran.name')
            ->join('kategori_pengeluaran', 'pengeluaran.category_id', '=', 'kategori_pengeluaran.id', 'LEFT')
            ->whereDate('pengeluaran.credit_date', '>=', $tanggal_awal)
            ->whereDate('pengeluaran.credit_date', '<=', $tanggal_akhir)
            ->paginate(10)
            ->appends(request()->except('page'));

        return view('account.laporan_credit.index', compact('credit', 'tanggal_awal', 'tanggal_akhir'));
    }
}
