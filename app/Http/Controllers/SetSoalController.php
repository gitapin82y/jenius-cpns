<?php

namespace App\Http\Controllers;

use App\Models\SetSoal;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SetSoalController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::user()->is_admin){
            return redirect('/');
        }
        if ($request->ajax()) {
            return DataTables::of(SetSoal::get())
                ->addColumn('status', function (SetSoal $setSoal) {
                    return $setSoal->status; // Using the new method
                })
                ->addColumn('action', function ($setSoal) {
                    $statusButton = $setSoal->status == 'Draf' ?
                    '<button type="button" class="btn btn-success btn-sm" onclick="changeStatus(' . $setSoal->id . ', \'Publish\')"><i class="fas fa-check-circle fa-sm text-white-50"></i> Publish</button>' :
                    '<button type="button" class="btn btn-warning btn-sm" onclick="changeStatus(' . $setSoal->id . ', \'Draf\')"><i class="fas fa-times-circle fa-sm text-white-50"></i> Draf</button>';

                return '
                    <a href="/soal/'.$setSoal->id.'" class="btn btn-info btn-sm"><i class="fas fa-eye fa-sm text-white-50"></i> Manage Soal</a> '
                    .$statusButton.'
                    <button type="button" class="btn btn-primary btn-sm" onclick="editSetSoal(' . htmlspecialchars(json_encode($setSoal), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-pen fa-sm text-white-50"></i> Edit</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $setSoal->id . ')"><i class="fas fa-trash fa-sm text-white-50"></i> Hapus</button>
                    ';
                })
                ->make(true);
        }

        return view('admin.setsoal.index');
    }

    public function changeStatus(Request $request, $id)
{
    $setSoal = SetSoal::findOrFail($id);
    $setSoal->status = $request->status;
    $setSoal->save();

    return response()->json([
        'status' => true,
        'message' => 'Status Berhasil Diperbarui!'
    ]);
}


    public function public()
    {
        $setSoals = SetSoal::where('status', 'Publish')
                              ->where('kategori', 'Tryout')
                              ->get();

        $akses = false;
        if(Auth::user()){
            $akses = true;   
        }

        $isLoggedIn = Auth::check();

        return view('login.set-soal.index', [
            'setSoals' => $setSoals,
            'userAkses' => $akses,
            'isLoggedIn' => $isLoggedIn
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'kategori' => 'required|in:Tryout,Latihan',
        ]);

        SetSoal::create($validatedData);

        return response()->json(['message' => 'Data Berhasil Disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'kategori' => 'required|in:Tryout,Latihan',
        ]);

        $setSoal = SetSoal::findOrFail($id);
        $setSoal->update($validatedData);

        return response()->json(['message' => 'Data Berhasil Diupdate!']);
    }

    public function destroy($id)
    {
        $setSoal = SetSoal::findOrFail($id);

        $setSoal->soal()->delete();

        $folderPath = 'public/set-soal-' . $setSoal->id;
        if (Storage::exists($folderPath)) {
            Storage::deleteDirectory($folderPath);
        }

        $setSoal->delete();

        return response()->json(['message' => 'Data Berhasil Dihapus!']);
    }
}

