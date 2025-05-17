<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SetSoal;
use App\Models\HasilTryout;
use App\Models\JawabanUser;
use App\Models\Soal;
use RealRashid\SweetAlert\Facades\Alert;

class TryoutController extends Controller
{
    public function index($set_soal_id)
    {
        $soals = Soal::where('set_soal_id', $set_soal_id)
    ->orderByRaw("FIELD(kategori, 'TWK', 'TIU', 'TKP')")
    ->get();
        $user = Auth::user();
        return view('login.tryout.index', compact('soals', 'user', 'set_soal_id'));
    }

    public function submit(Request $request)
    {
        $jawabanUsers = $request->input('jawaban_users');
        $set_soal_id = $request->input('set_soal_id');
        $user = Auth::user();

        if ($user->paket_id == 1) {
            $user->is_akses = false;
            $user->save();
        }

        $soals = Soal::where('set_soal_id', $set_soal_id)->get();
    
        // Initialize category and type scores
        $scores = [
            'twk_score' => 0,
            'tiu_score' => 0,
            'tkp_score' => 0,
            'total_benar' => 0,
            'total_salah' => 0,
            'total_kosong' => 0,
            'nasionalisme' => 0,
            'integritas' => 0,
            'bela_negara' => 0,
            'pilar_negara' => 0,
            'bahasa_indonesia' => 0,
            'verbal_analogi' => 0,
            'verbal_silogisme' => 0,
            'verbal_analisis' => 0,
            'numerik_hitung_cepat' => 0,
            'numerik_deret_angka' => 0,
            'numerik_perbandingan_kuantitatif' => 0,
            'numerik_soal_cerita' => 0,
            'figural_analogi' => 0,
            'figural_ketidaksamaan' => 0,
            'figural_serial' => 0,
            'pelayanan_publik' => 0,
            'jejaring_kerja' => 0,
            'sosial_budaya' => 0,
            'teknologi_informasi_dan_komunikasi_tik' => 0,
            'profesionalisme' => 0,
            'anti_radikalisme' => 0,
        ];
        foreach ($soals as $soal) {
            $soal_id = $soal->id;
            $jawaban_user = $jawabanUsers[$soal_id] ?? null; // Get user answer or null if not set
            $status = 'kosong'; // Default status for unanswered questions
    
            $tipe_key = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $soal->tipe), '_'));

            if($soal->kategori == "TKP"){
                if ($jawaban_user !== null) {
                   $status = 'benar';
                   $scores['tkp_score'] += $soal['score_'.strtolower($jawaban_user)];
                    $scores['total_benar']++;
                } else {
                    $scores['total_kosong']++;
                }

                if (array_key_exists($tipe_key, $scores) && $status == 'benar') {
                    $scores[$tipe_key] += $soal['score_'.strtolower($jawaban_user)];
                }
            }else{
                if ($jawaban_user === $soal->jawaban_benar) {
                    $status = 'benar';
                    $scores[strtolower($soal->kategori) . '_score'] += $soal->poin;
                    $scores['total_benar']++;
                } elseif ($jawaban_user !== null) {
                    $status = 'salah';
                    $scores['total_salah']++;
                } else {
                    $scores['total_kosong']++;
                }

                if (array_key_exists($tipe_key, $scores) && $status == 'benar') {
                    $scores[$tipe_key] += $soal->poin;
                }
            }
    
            JawabanUser::updateOrCreate(
                ['user_id' => $user->id, 'soal_id' => $soal_id],
                ['set_soal_id' => $set_soal_id, 'jawaban_user' => $jawaban_user, 'status' => $status]
            );
        }
        
        HasilTryout::updateOrCreate(
            ['user_id' => $user->id, 'set_soal_id' => $set_soal_id],
            $scores
        );
    
        return redirect()->route('tryout.result', $set_soal_id);
    }
    
    
    public function result($set_soal)
    {
        $userId = Auth::user()->id;
        $hasilTryout = HasilTryout::where('user_id',$userId)->where('set_soal_id',$set_soal)->first();
        if(!$hasilTryout){
            toast()->error('Anda belum mengerjakan tryout!');
            return redirect()->back();
        }
        
        $soals = Soal::where('set_soal_id', $set_soal)->get();
        $hasilTryout['total_poin'] = $soals->count() * 5;
        $skbSetSoal = SetSoal::findOrFail($set_soal);
        $jawabanUsers = JawabanUser::where('user_id', $userId)->where('set_soal_id', $set_soal)->with('soal')->get();
        return view('login.tryout.hasil', compact('soals','skbSetSoal','hasilTryout', 'jawabanUsers'));
    }
    
}