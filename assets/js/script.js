document.addEventListener('DOMContentLoaded', function() {
    
    const inputFoto = document.getElementById('inputFoto');
    const preview = document.getElementById('preview');
    if (inputFoto && preview) {
        inputFoto.addEventListener('change', evt => {
            const [file] = inputFoto.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });
    }

    const selectProvinsi = document.getElementById('pilihProvinsi');
    const selectKabkot = document.getElementById('pilihKabkot');

    if (selectProvinsi && selectKabkot) {
        selectProvinsi.addEventListener('change', function() {
            const idProvinsi = this.value;

            if (!idProvinsi) {
                selectKabkot.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
                selectKabkot.disabled = true;
                return;
            }
            selectKabkot.innerHTML = '<option value="">Memuat data...</option>';
            selectKabkot.disabled = true;

            fetch(`get_kabkot.php?id_prov=${idProvinsi}`)
                .then(response => response.json())
                .then(data => {
                    selectKabkot.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
                    
                    data.forEach(kabkot => {
                        const option = document.createElement('option');
                        option.value = kabkot.id;
                        option.textContent = kabkot.nama_kabkot; 
                        selectKabkot.appendChild(option);
                    });

                    selectKabkot.disabled = false; 
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                    selectKabkot.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        });
    }

    const form = document.getElementById('formPendaftaran');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nama = document.querySelector('input[name="nama"]').value;
            const notelp = document.querySelector('input[name="notelp"]').value;
            const jk = document.querySelector('input[name="jk"]:checked');

            if (nama.trim().length < 3) {
                e.preventDefault();
                Swal.fire('Oops!', 'Nama minimal 3 karakter', 'error');
                return;
            }

            if (!jk) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Silakan pilih Jenis Kelamin', 'warning');
                return;
            }
            if (notelp !== "" && !/^\d+$/.test(notelp)) {
                e.preventDefault();
                Swal.fire('Error', 'Nomor telepon harus berupa angka!', 'error');
                return;
            }
        });
    }

    const searchInput = document.getElementById('cariSiswa');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
        searchInput.addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                if (row.cells.length > 0) {
                    let nama = row.cells[0].textContent.toLowerCase();
                    row.style.display = nama.includes(filter) ? '' : 'none';
                }
            });
        });
    }
});


function hapusData(url) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}