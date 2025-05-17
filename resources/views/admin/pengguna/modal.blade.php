<!-- Letakkan ini di resources/views/admin/pengguna/modal.blade.php -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm" action="{{ route('pengguna.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" required class="form-control" id="name" name="name" value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" required class="form-control" id="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                    </div>
                    <div class="form-group">
                        <label for="province_id">Provinsi</label>
                        <select class="form-control" required id="province_id" name="province_id" style="width: 100%;">
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinces as $item)
                                <option value="{{ $item->id ?? '' }}">{{ $item->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city_id">Kota/Kabupaten</label>
                        <select class="form-control" required id="city_id" name="city_id" style="width: 100%;">
                            <option value="">Pilih Kota/Kabupaten</option>
                            <!-- Populate with API -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="last_education">Pendidikan Terakhir</label>
                        <input type="text" class="form-control" id="last_education" name="last_education" value="{{ old('last_education') }}">
                    </div>
                    <div class="form-group">
                        <label for="major">Jurusan</label>
                        <input type="text" class="form-control" id="major" name="major" value="{{ old('major') }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                    </div>
                    <div class="form-group">
                        <label for="paket_id">Paket</label>
                        <select class="form-control" required id="paket_id" name="paket_id" style="width: 100%;">
                            <option></option>
                            @foreach($pakets as $paket)
                                <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="daftar_member">Daftar Member</label>
                        <input type="date" class="form-control" id="daftar_member" name="daftar_member" value="{{ old('daftar_member') }}">
                    </div>
                    <div class="form-group">
                        <label for="selesai_member">Selesai Member</label>
                        <input type="date" class="form-control" id="selesai_member" name="selesai_member" value="{{ old('selesai_member') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
