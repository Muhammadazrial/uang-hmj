<?php

namespace App\Http\Controllers\account;

use App\Models\CategoriesDebit;
use App\Models\Debit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebitController extends Controller
{
    /**
     * DebitController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $debit = DB::table('pemasukan')
            ->select('pemasukan.id', 'pemasukan.category_id', 'pemasukan.user_id', 'pemasukan.nominal', 'pemasukan.debit_date', 'pemasukan.description', 'kategori_pemasukan.id as id_category', 'kategori_pemasukan.name')
            ->join('kategori_pemasukan', 'pemasukan.category_id', '=', 'kategori_pemasukan.id', 'LEFT')
            ->where('pemasukan.user_id', Auth::user()->id)
            ->orderBy('pemasukan.created_at', 'DESC')
            ->paginate(10);
        return view('account.debit.index', compact('debit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $debit = DB::table('pemasukan')
            ->select('pemasukan.id', 'pemasukan.category_id', 'pemasukan.user_id', 'pemasukan.nominal', 'pemasukan.debit_date', 'pemasukan.description', 'kategori_pemasukan.id as id_category', 'kategori_pemasukan.name')
            ->join('kategori_pemasukan', 'debit.category_id', '=', 'kategori_pemasukan.id', 'LEFT')
            ->where('pemasukan.user_id', Auth::user()->id)
            ->where('pemasukan.description', 'LIKE', '%' .$search. '%')
            ->orderBy('pemasukan.created_at', 'DESC')
            ->paginate(10);
        return view('account.debit.index', compact('debit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = CategoriesDebit::where('user_id', Auth::user()->id)
        ->get();
        return view('account.debit.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //set validasi required
        $this->validate($request, [
            'nominal'       => 'required',
            'debit_date'    => 'required',
            'category_id'   => 'required',
            'description'   => 'required'
        ],
            //set message validation
            [
                'nominal.required' => 'Masukkan Nominal Debit / Uang Masuk!',
                'debit_date.required' => 'Silahkan Pilih Tanggal!',
                'category_id.required' => 'Silahkan Pilih Kategori!',
                'description.required' => 'Masukkan Keterangan!',
            ]
        );

        //Eloquent simpan data
        $save = Debit::create([
            'user_id'       => Auth::user()->id,
            'debit_date'   => $request->input('debit_date'),
            'category_id'   => $request->input('category_id'),
            'nominal'       => str_replace(",", "", $request->input('nominal')),
            'description'   => $request->input('description'),
        ]);
        //cek apakah data berhasil disimpan
        if($save){
            //redirect dengan pesan sukses
            return redirect()->route('account.debit.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('account.debit.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Debit $debit)
    {
        $categories = CategoriesDebit::where('user_id', Auth::user()->id)
            ->get();
        return  view('account.debit.edit', compact('debit', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Debit $debit)
    {
        //set validasi required
        $this->validate($request, [
            'nominal'       => 'required',
            'debit_date'    => 'required',
            'category_id'   => 'required',
            'description'   => 'required'
        ],
            //set message validation
            [
                'nominal.required' => 'Masukkan Nominal Debit / Uang Masuk!',
                'debit_date.required' => 'Silahkan Pilih Tanggal!',
                'category_id.required' => 'Silahkan Pilih Kategori!',
                'description.required' => 'Masukkan Keterangan!',
            ]
        );

        //Eloquent simpan data
        $update = Debit::whereId($debit->id)->update([
            'user_id'       => Auth::user()->id,
            'category_id'   => $request->input('category_id'),
            'debit_date'    => $request->input('debit_date'),
            'nominal'       => str_replace(",", "", $request->input('nominal')),
            'description'   => $request->input('description'),
        ]);
        //cek apakah data berhasil disimpan
        if($update){
            //redirect dengan pesan sukses
            return redirect()->route('account.debit.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('account.debit.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = Debit::find($id)->delete($id);

        if($delete){
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
