<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\SetSoal;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\KeywordExtractionService;

class SoalController extends Controller
{
        private $keywordService;

    public function __construct(KeywordExtractionService $keywordService)
    {
        $this->keywordService = $keywordService;
    }
    public function index(Request $request, $id)
    {
        if(!Auth::user()->is_admin){
            return redirect('/');
        }
        if ($request->ajax()) {
            return DataTables::of(Soal::where('set_soal_id', $id)->latest())
            ->editColumn('poin', function ($soal) {
                return $soal->kategori == 'TKP' ? '-' : $soal->poin;
            })    
            ->editColumn('jawaban_benar', function ($soal) {
                return $soal->kategori == 'TKP' ? 'A,B,C,D,E' : $soal->jawaban_benar;
            })    
            ->addColumn('pertanyaan', function($soal) {
                return Str::limit($soal->pertanyaan, 20, '...');
            })
             ->addColumn('kata_kunci_display', function($soal) {
                if ($soal->kata_kunci) {
                    $keywords = json_decode($soal->kata_kunci, true);
                    if (is_array($keywords) && count($keywords) > 0) {
                        $badges = '';
                        $displayKeywords = array_slice($keywords, 0, 3); // Tampilkan 3 kata kunci pertama
                        
                        foreach ($displayKeywords as $keyword) {
                            $badges .= '<span class="badge badge-info me-1 mb-1">' . htmlspecialchars($keyword) . '</span>';
                        }
                        
                        if (count($keywords) > 3) {
                            $badges .= '<span class="badge badge-secondary">+' . (count($keywords) - 3) . '</span>';
                        }
                        
                        return $badges;
                    }
                }
                return '<span class="text-muted"><em>Auto-generate saat edit</em></span>';
            })
            ->addColumn('action', function ($soal) {
                    return '
                    <button type="button" class="btn btn-info btn-sm mt-1" onclick="showDetailSoalModal(' . htmlspecialchars(json_encode($soal), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-eye fa-sm text-white-50"></i> Selengkapnya</button>
                        <button type="button" class="btn btn-primary btn-sm mt-1" onclick="editSoal(' . htmlspecialchars(json_encode($soal), ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-pen fa-sm text-white-50"></i> Ubah</button>
                        <form action="' . route('soal.destroy', $soal->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash fa-sm text-white-50"></i> Hapus</button>
                        </form>';
                })
                 ->rawColumns(['action', 'kata_kunci_display'])
                ->make(true);
        }

        $setSoal = SetSoal::findOrFail($id);

        return view('admin.soal.index', compact('setSoal'));
    }

    public function store(Request $request)
    {
        // Map tipe to kategori
        $kategoriMap = [
            'Nasionalisme' => 'TWK',
            'Integritas' => 'TWK',
            'Bela Negara' => 'TWK',
            'Pilar Negara' => 'TWK',
            'Bahasa Indonesia' => 'TWK',
            'Verbal (Analogi)' => 'TIU',
            'Verbal (Silogisme)' => 'TIU',
            'Verbal (Analisis)' => 'TIU',
            'Numerik (Hitung Cepat)' => 'TIU',
            'Numerik (Deret Angka)' => 'TIU',
            'Numerik (Perbandingan Kuantitatif)' => 'TIU',
            'Numerik (Soal Cerita)' => 'TIU',
            'Figural (Analogi)' => 'TIU',
            'Figural (Ketidaksamaan)' => 'TIU',
            'Figural (Serial)' => 'TIU',
            'Pelayanan Publik' => 'TKP',
            'Jejaring Kerja' => 'TKP',
            'Sosial Budaya' => 'TKP',
            'Teknologi Informasi dan Komunikasi (TIK)' => 'TKP',
            'Profesionalisme' => 'TKP',
            'Anti Radikalisme' => 'TKP',
        ];
    
        $kategori = $kategoriMap[$request->input('tipe')];
        
        // Basic validation rules
        $rules = [
            'set_soal_id' => 'required|integer',
            'tipe' => 'required|string',
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'pembahasan' => 'required|string',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ];
    
        // Additional validation for non-TKP categories
        if ($kategori !== 'TKP') {
            $rules['poin'] = 'required|integer';
            $rules['jawaban_benar'] = 'required|string';
            $rules['score_a'] = 'nullable|integer';
            $rules['score_b'] = 'nullable|integer';
            $rules['score_c'] = 'nullable|integer';
            $rules['score_d'] = 'nullable|integer';
            $rules['score_e'] = 'nullable|integer';
        }else{
            $rules['score_a'] = 'required|integer';
            $rules['score_b'] = 'required|integer';
            $rules['score_c'] = 'required|integer';
            $rules['score_d'] = 'required|integer';
            $rules['score_e'] = 'required|integer';
        }
    
        $validatedData = $request->validate($rules);
    
        $validatedData['kategori'] = $kategori;
    
        if ($kategori === 'TKP') {
            $validatedData['poin'] = 0;
            $validatedData['jawaban_benar'] = null;
        } else {
            $validatedData['score_a'] = null;
            $validatedData['score_b'] = null;
            $validatedData['score_c'] = null;
            $validatedData['score_d'] = null;
            $validatedData['score_e'] = null;
        }

         // Generate kata kunci otomatis jika tidak diisi
        if (empty($validatedData['kata_kunci'])) {
            $keywords = $this->keywordService->extractKeywords(
                $validatedData['pertanyaan'], 
                $validatedData['tipe']
            );
            $validatedData['kata_kunci'] = json_encode($keywords);
        } else {
            // Jika diisi manual, convert ke array JSON
            $manualKeywords = array_map('trim', explode(',', $validatedData['kata_kunci']));
            $validatedData['kata_kunci'] = json_encode($manualKeywords);
        }
    
        $setSoal = SetSoal::findOrFail($validatedData['set_soal_id']);
        if ($request->hasFile('foto')) {
            $folderPath = 'set-soal-' . $setSoal->id;
            $validatedData['foto'] = $request->file('foto')->store($folderPath, 'public');
        }    
    
        Soal::create($validatedData);
    
        $setSoal->increment('jumlah_soal');
    
        return response()->json(['message' => 'Data Berhasil Disimpan!']);
    }

    
    public function update(Request $request, $id)
    {
        $soal = Soal::findOrFail($id);
    
        // Map tipe to kategori
        $kategoriMap = [
            'Nasionalisme' => 'TWK',
            'Integritas' => 'TWK',
            'Bela Negara' => 'TWK',
            'Pilar Negara' => 'TWK',
            'Bahasa Indonesia' => 'TWK',
            'Verbal (Analogi)' => 'TIU',
            'Verbal (Silogisme)' => 'TIU',
            'Verbal (Analisis)' => 'TIU',
            'Numerik (Hitung Cepat)' => 'TIU',
            'Numerik (Deret Angka)' => 'TIU',
            'Numerik (Perbandingan Kuantitatif)' => 'TIU',
            'Numerik (Soal Cerita)' => 'TIU',
            'Figural (Analogi)' => 'TIU',
            'Figural (Ketidaksamaan)' => 'TIU',
            'Figural (Serial)' => 'TIU',
            'Pelayanan Publik' => 'TKP',
            'Jejaring Kerja' => 'TKP',
            'Sosial Budaya' => 'TKP',
            'Teknologi Informasi dan Komunikasi (TIK)' => 'TKP',
            'Profesionalisme' => 'TKP',
            'Anti Radikalisme' => 'TKP',
        ];
    
        $kategori = $kategoriMap[$request->input('tipe')];
    
        // Basic validation rules
        $rules = [
            'tipe' => 'required|string',
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'pembahasan' => 'required|string',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ];
    
        // Additional validation for non-TKP categories
        if ($kategori !== 'TKP') {
            $rules['poin'] = 'required|integer';
            $rules['jawaban_benar'] = 'required|string';
            $rules['score_a'] = 'nullable|integer';
            $rules['score_b'] = 'nullable|integer';
            $rules['score_c'] = 'nullable|integer';
            $rules['score_d'] = 'nullable|integer';
            $rules['score_e'] = 'nullable|integer';
        }else{
            $rules['score_a'] = 'required|integer';
            $rules['score_b'] = 'required|integer';
            $rules['score_c'] = 'required|integer';
            $rules['score_d'] = 'required|integer';
            $rules['score_e'] = 'required|integer';
        }
    
        $validatedData = $request->validate($rules);
    
        $validatedData['kategori'] = $kategori;
    
        if ($kategori === 'TKP') {
            $validatedData['poin'] = 0;
            $validatedData['jawaban_benar'] = null;
        } else {
            $validatedData['score_a'] = null;
            $validatedData['score_b'] = null;
            $validatedData['score_c'] = null;
            $validatedData['score_d'] = null;
            $validatedData['score_e'] = null;
        }
    
          // Generate kata kunci otomatis jika tidak diisi
        if (empty($validatedData['kata_kunci'])) {
            $keywords = $this->keywordService->extractKeywords(
                $validatedData['pertanyaan'], 
                $validatedData['tipe']
            );
            $validatedData['kata_kunci'] = json_encode($keywords);
        } else {
            // Jika diisi manual, convert ke array JSON
            $manualKeywords = array_map('trim', explode(',', $validatedData['kata_kunci']));
            $validatedData['kata_kunci'] = json_encode($manualKeywords);
        }

        if ($request->hasFile('foto')) {
            if ($soal->foto) {
                Storage::delete('public/' . $soal->foto);
            }
            $setSoal = SetSoal::findOrFail($soal->set_soal_id);
            $folderPath = 'set-soal-' . $setSoal->id;
            $validatedData['foto'] = $request->file('foto')->store($folderPath, 'public');
        }
    
        $soal->update($validatedData);
    
        return response()->json(['message' => 'Data Berhasil diubah!']);
    }

        public function getKeywordSuggestions(Request $request)
    {
        $tipe = $request->input('tipe');
        $pertanyaan = $request->input('pertanyaan', '');
        
        if (!$tipe) {
            return response()->json(['keywords' => []]);
        }
        
        $keywords = $this->keywordService->extractKeywords($pertanyaan, $tipe);
        
        return response()->json(['keywords' => $keywords]);
    }

    

    
    public function destroy($id)
    {
        $soal = Soal::findOrFail($id);
        $soal->jawabanUsers()->delete();

        if ($soal->foto) {
            Storage::delete('public/' . $soal->foto);
        }

        $setSoal = SetSoal::findOrFail($soal->set_soal_id);
        $setSoal->decrement('jumlah_soal');

        $soal->delete();

        // return redirect()->route('soal.index', $soal->set_soal_id)->with('success', 'Data soal berhasil dihapus');
        toast()->success('Data Berhasil Dihapus!');
        return redirect()->back();
    }

}


