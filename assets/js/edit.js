document.addEventListener('DOMContentLoaded', function() {
    
    const inputFoto = document.getElementById('inputFotoEdit');
    const previewBaru = document.getElementById('previewBaru');
    const previewLama = document.getElementById('previewLama');

    if (inputFoto && previewBaru) {
        inputFoto.onchange = evt => {
            const [file] = inputFoto.files;
            if (file) {
                previewBaru.src = URL.createObjectURL(file);
                previewBaru.style.display = 'block';
                if (previewLama) previewLama.style.opacity = '0.3';
            }
        }
    }

    const form = document.getElementById('formEdit');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const nama = document.querySelector('input[name="nama"]').value;
            const notelp = document.querySelector('input[name="notelp"]').value;

            if (nama.trim().length < 3) {
                e.preventDefault(); 
                Swal.fire('Oops!', 'Nama minimal 3 karakter!', 'error');
                return;
            }
            
            if (notelp !== "" && !/^\d+$/.test(notelp)) {
                e.preventDefault(); 
                Swal.fire('Error', 'Nomor telepon harus berupa angka!', 'error');
                return;
            }
            
            console.log("Validasi Berhasil!");
        });
    }

    const selectProvinsi = document.getElementById('pilihProvinsi');
    const selectKabkot = document.getElementById('pilihKabkot');

    function loadKabkot(idProvinsi, idKabkotSelected = null) {
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
                    
                    if (idKabkotSelected && kabkot.id == idKabkotSelected) {
                        option.selected = true;
                    }
                    
                    selectKabkot.appendChild(option);
                });

                selectKabkot.disabled = false;
            })
            .catch(error => {
                console.error('Terjadi kesalahan:', error);
                selectKabkot.innerHTML = '<option value="">Gagal memuat data</option>';
            });
    }

    if (selectProvinsi && selectKabkot) {
        const savedProvId = selectProvinsi.value;
        const savedKabkotId = selectKabkot.getAttribute('data-selected');

        if (savedProvId) {
            loadKabkot(savedProvId, savedKabkotId);
        }
        selectProvinsi.addEventListener('change', function() {
            loadKabkot(this.value, null); 
        });
    }
});