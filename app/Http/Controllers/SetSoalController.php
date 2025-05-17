<?php

namespace App\Http\Controllers;

use App\Models\SetSoal;
use App\Models\Paket;
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
            return DataTables::of(SetSoal::with('paket')->get())
                ->addColumn('paket_id', function (SetSoal $setSoal) {
                    return $setSoal->paket->nama_paket;
                })
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
        $countFree = SetSoal::whereHas('paket', function ($query) {
            $query->where('nama_paket', 'Free');
        })->count();
    
        $countCeban = SetSoal::whereHas('paket', function ($query) {
            $query->where('nama_paket', 'Ceban');
        })->count();
    
        $countSaban = SetSoal::whereHas('paket', function ($query) {
            $query->where('nama_paket', 'Saban');
        })->count();
    
        $countGocap = SetSoal::whereHas('paket', function ($query) {
            $query->where('nama_paket', 'Gocap');
        })->count();

        $pakets = Paket::all();

        return view('admin.setsoal.index', compact('countFree', 'countCeban', 'countSaban', 'countGocap','pakets'));
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

    public function getCounts()
{
    $countFree = SetSoal::whereHas('paket', function ($query) {
        $query->where('nama_paket', 'Free');
    })->count();

    $countCeban = SetSoal::whereHas('paket', function ($query) {
        $query->where('nama_paket', 'Ceban');
    })->count();

    $countSaban = SetSoal::whereHas('paket', function ($query) {
        $query->where('nama_paket', 'Saban');
    })->count();

    $countGocap = SetSoal::whereHas('paket', function ($query) {
        $query->where('nama_paket', 'Gocap');
    })->count();

    return response()->json([
        'countFree' => $countFree,
        'countCeban' => $countCeban,
        'countSaban' => $countSaban,
        'countGocap' => $countGocap,
    ]);
}

    public function public()
    {
        $setSoals = SetSoal::with('paket') // Eager load relasi Paket
            ->where('status','Publish')
            ->orderBy('paket_id') // Urutkan berdasarkan paket_id
            ->get();

            $user = Auth::user();
            $now = Carbon::now();
            $start = Carbon::parse($user->daftar_member);
            $end = Carbon::parse($user->selesai_member);
        
            // Menghitung durasi dalam bulan
            $remainingDays = $now->diffInDays($end, false);
            
            if (round($remainingDays) <= 0 && $user->paket_id > 1) {
                $user->update(['paket_id' => 1,'is_akses' => 0]);
                $statusMessage = 'Tidak memiliki akses tryout!';
            } else {
                $statusMessage = "Masa aktif tersisa: " . round($remainingDays) . " hari";
            }

        return view('login.set-soal.index', [
            'setSoals' => $setSoals,
            'user' => $user,
            'userPaketId' => $user->paket_id,
            'userAkses' => $user->is_akses,
            'statusMessage' => $statusMessage,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'paket_id' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);

        SetSoal::create($validatedData);

        return response()->json(['message' => 'Data Berhasil Disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'paket_id' => 'required|integer',
            'title' => 'required|string|max:255',
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

