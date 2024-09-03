<?php

namespace App\Http\Controllers;

use App\Models\KtgMateriModel;
use Illuminate\Http\Request;
Use App\Models\KursusModel;
use App\Models\MateriModel;
use App\Models\PdKursusModel;

class KursusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = KursusModel::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_krs', 'like', "%{$search}%");
            });

            $courses = $query->latest('created_at')->get();

            if ($courses->isEmpty()) {
                return redirect()->route('courses.index')
                                 ->with('error', 'Tidak ada hasil ditemukan untuk pencarian: ' . $search)
                                 ->withInput();
            }
        } else {
            $courses = KursusModel::latest('created_at')->get();
        }
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = KtgMateriModel::latest('created_at')->get();
        $materies = MateriModel::latest('created_at')->get();
        return view('courses.create', compact('categories', 'materies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_krs' => 'required|unique:kursus,nama_krs|regex:/^[a-zA-Z\s]+$/',
            'gambar' => 'required|image|max:5280|mimes:jpeg,png,jpg',
            'deskripsi' => 'required|min:10',
            'id_mtr' => 'required|exists:materi,id',
            'biaya_krs' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'jam' => 'required|integer|min:1'
        ], [
            'nama_krs.required' => 'Nama kursus wajib diisi.',
            'nama_krs.unique' => 'Nama kursus sudah ada, silahkan masukkan nama yang lain.',
            'nama_krs.regex' => 'Nama kursus hanya boleh terdiri dari huruf.',
            'gambar.required' => 'Mohon lampirkan gambar.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus berupa jpeg, png, jpg, atau gif.',
            'gambar.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'deskripsi.required' => 'Deskripsi kursus wajib diisi.',
            'deskripsi.min' => 'Deskripsi minimal memiliki 10 karakter.',
            'id_mtr.required' => 'Materi tidak boleh kosong.',
            'id_mtr.exists' => 'Materi tidak valid.',
            'biaya_krs.required' => 'Biaya kursus wajib diisi.',
            'biaya_krs.numeric' => 'Biaya kursus hanya boleh berupa angka.',
            'biaya_krs.min' => 'Biaya kursus tidak boleh kurang dari 0.',
            'durasi.required' => 'Durasi kursus wajib diisi.',
            'durasi.integer' => 'Durasi kursus harus berupa bilangan bulat.',
            'durasi.min' => 'Durasi kursus tidak boleh dibawah 0.',
            'jam.required' => 'Durasi jam perhari wajib diisi.',
            'jam.integer' => 'Durasi jam perhari harus berupa bilangan bulat.',
            'jam.min' => 'Durasi jam perhari tidak boleh dibawah 0.'
        ]);

        if($request->hasFile('gambar')){
            $file = $request->file('gambar');
            $filename=time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            try {
                KursusModel::create([
                    'nama_krs' => $request->nama_krs,
                    'gambar' => $filename,
                    'deskripsi' => $request->deskripsi,
                    'id_mtr' => $request->id_mtr,
                    'biaya_krs' => $request->biaya_krs,
                    'durasi' => $request->durasi,
                    'jam' => $request->jam
                ]);

                return redirect('/courses')->with('success', 'Kursus baru berhasil ditambahkan');
            } catch(\Exception $e) {
                return redirect('/courses')->with('error', 'Kursus baru gagal untuk di tambahkan');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request){
    $course = KursusModel::with('materi')->findOrFail($id);

    $search = $request->input('search');
    $query = PdKursusModel::where('id_krs', $id)->with('murid');

    if ($search) {
        $query->whereHas('murid', function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%");
        });

        $regists = $query->latest('created_at')->get();

        if ($regists->isEmpty()) {
            return back()
                             ->with('error', 'Tidak ada hasil ditemukan untuk pencarian: ' . $search)
                             ->withInput();
        }
    } else {
        $regists = PdKursusModel::latest('tanggal_mulai')->get();
    }
    $regists = PdKursusModel::where('id_krs', $id)->with('kursus', 'murid')->get();
    
    // Mengirim data ke view
    return view('courses.show', compact('course', 'regists'));}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = KursusModel::findOrFail($id);
        $categories = KtgMateriModel::latest('created_at')->get();
        $materies = MateriModel::latest('created_at')->get();
        return view('courses.edit', compact('course','categories', 'materies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kursus = KursusModel::find($id);

        $request->validate([
            'nama_krs' => 'required|unique:kursus,nama_krs,' . $id . '|regex:/^[a-zA-Z\s]+$/',
            'gambar' => 'nullable|image|max:5280|mimes:jpeg,png,jpg',
            'deskripsi' => 'required|min:10',
            'id_mtr' => 'required|exists:materi,id',
            'biaya_krs' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'jam' => 'required|integer|min:1'
        ], [
            'nama_krs.required' => 'Nama kursus wajib diisi.',
            'nama_krs.unique' => 'Nama kursus sudah ada, silahkan masukkan nama yang lain.',
            'nama_krs.regex' => 'Nama kursus hanya boleh terdiri dari huruf.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus berupa jpeg, png, jpg.',
            'gambar.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            'deskripsi.required' => 'Deskripsi kursus wajib diisi.',
            'deskripsi.min' => 'Deskripsi minimal memiliki 10 karakter.',
            'id_mtr.required' => 'Materi tidak boleh kosong.',
            'id_mtr.exists' => 'Materi tidak valid.',
            'biaya_krs.required' => 'Biaya kursus wajib diisi.',
            'biaya_krs.numeric' => 'Biaya kursus hanya boleh berupa angka.',
            'biaya_krs.min' => 'Biaya kursus tidak boleh kurang dari 0.',
            'durasi.required' => 'Durasi kursus wajib diisi.',
            'durasi.integer' => 'Durasi kursus harus berupa bilangan bulat.',
            'durasi.min' => 'Durasi kursus tidak boleh dibawah 1.',
            'jam.required' => 'Durasi jam perhari wajib diisi.',
            'jam.integer' => 'Durasi jam perhari harus berupa bilangan bulat.',
            'jam.min' => 'Durasi jam perhari tidak boleh dibawah 1.'
        ]);

        // Cek jika ada file gambar yang diunggah
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Hapus file gambar lama jika ada
            if ($kursus->gambar && file_exists(public_path('assets/images/' . $kursus->gambar))) {
                unlink(public_path('assets/images/' . $kursus->gambar));
            }

            // Pindahkan file gambar baru ke folder assets/images
            $file->move(public_path('assets/images'), $filename);

            // Update nama file gambar di database
            $kursus->gambar = $filename;
        }

        try {
            // Update data kursus
            $kursus->update([
                'nama_krs' => $request->nama_krs,
                'gambar' => $kursus->gambar ?? $kursus->gambar,  // Tetap gunakan gambar lama jika tidak diubah
                'deskripsi' => $request->deskripsi,
                'id_mtr' => $request->id_mtr,
                'biaya_krs' => $request->biaya_krs,
                'durasi' => $request->durasi,
                'jam' => $request->jam
            ]);

            return redirect('/courses')->with('success', 'Kursus berhasil diedit');
        } catch (\Exception $e) {
            return redirect('/courses')->with('error', 'Kursus gagal diedit.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $kursus = KursusModel::find($id);

    try {
        if ($kursus->gambar && file_exists(public_path('assets/images' . $kursus->gambar))) {
            unlink(public_path('assets/images' . $kursus->gambar));
        }
        $kursus->delete();

        return redirect()->route('coursees.index')->with('success', 'Kursus berhasil dihapus.');
    } catch (\Exception $e) {
        if ($e->getCode() == 23000) {
            return redirect()->route('courses.index')->with('error', 'Kursus tidak dapat dihapus karena masih terkait dengan pendaftaran kursus.');
        }

        return redirect()->route('courses.index')->with('error', 'Gagal menghapus kursus.');
    }
}

}
