<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              
              <div class="d-sm-flex align-items-center justify-content-between border-bottom mb-4">
                <h3 class="mb-0">
                  <i class="mdi mdi-truck"></i>
                  <?php echo $data['action'] === 'create' ? 'Tambah Ekspedisi' : 'Edit Ekspedisi'; ?>
                </h3>
                <div class="d-flex gap-2">
                  <a href="/web_scm/ekspedisi" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                  </a>
                  <?php if ($data['action'] === 'edit'): ?>
                    <button type="button" class="btn btn-info" onclick="viewEkspedisiDetails()">
                      <i class="mdi mdi-eye"></i> Detail
                    </button>
                  <?php endif; ?>
                </div>
              </div>

              <?php if (isset($data['error']) && !empty($data['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-alert-circle"></i>
                  <?php echo htmlspecialchars($data['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if (isset($data['success']) && !empty($data['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-check-circle"></i>
                  <?php echo htmlspecialchars($data['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="row">
                <div class="col-lg-8">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="mdi mdi-form-select"></i>
                        Form <?php echo $data['action'] === 'create' ? 'Tambah' : 'Edit'; ?> Ekspedisi
                      </h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" action="<?php echo $data['action'] === 'create' ? '/web_scm/ekspedisi/store' : '/web_scm/ekspedisi/update'; ?>" id="ekspedisiForm">
                        
                        <?php if ($data['action'] === 'edit' && $data['ekspedisi']): ?>
                          <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['ekspedisi']['id']); ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label for="kode" class="form-label">
                                Kode Ekspedisi <span class="text-danger">*</span>
                                <i class="mdi mdi-help-circle" data-bs-toggle="tooltip" title="Kode harus sesuai dengan Raja Ongkir API"></i>
                              </label>
                              <?php if ($data['action'] === 'create'): ?>
                                <select class="form-select" id="kode" name="kode" required>
                                  <option value="">Pilih Kode Ekspedisi</option>
                                  <?php if (!empty($data['available_codes'])): ?>
                                    <?php foreach ($data['available_codes'] as $code): ?>
                                      <option value="<?php echo htmlspecialchars($code['kode']); ?>">
                                        <?php echo htmlspecialchars(strtoupper($code['kode'])); ?> - <?php echo htmlspecialchars($code['nama']); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  <?php else: ?>
                                    <option value="jne">JNE - JNE</option>
                                    <option value="pos">POS - POS Indonesia</option>
                                    <option value="tiki">TIKI - TIKI</option>
                                  <?php endif; ?>
                                </select>
                              <?php else: ?>
                                <input 
                                  type="text" 
                                  class="form-control" 
                                  id="kode" 
                                  name="kode" 
                                  value="<?php echo $data['ekspedisi'] ? htmlspecialchars($data['ekspedisi']['kode']) : ''; ?>" 
                                  required
                                  readonly
                                  style="background-color: #f8f9fa;"
                                >
                                <div class="form-text">Kode ekspedisi tidak dapat diubah</div>
                              <?php endif; ?>
                            </div>
                          </div>
                          
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label for="nama" class="form-label">
                                Nama Ekspedisi <span class="text-danger">*</span>
                              </label>
                              <input 
                                type="text" 
                                class="form-control" 
                                id="nama" 
                                name="nama" 
                                value="<?php echo $data['ekspedisi'] ? htmlspecialchars($data['ekspedisi']['nama']) : ''; ?>" 
                                required
                                placeholder="Masukkan nama ekspedisi"
                                maxlength="50"
                              >
                              <div class="form-text">Maksimal 50 karakter</div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label for="status" class="form-label">Status</label>
                              <select class="form-select" id="status" name="status">
                                <option value="aktif" <?php echo ($data['ekspedisi'] && $data['ekspedisi']['status'] === 'aktif') || !$data['ekspedisi'] ? 'selected' : ''; ?>>
                                  Aktif
                                </option>
                                <option value="nonaktif" <?php echo ($data['ekspedisi'] && $data['ekspedisi']['status'] === 'nonaktif') ? 'selected' : ''; ?>>
                                  Nonaktif
                                </option>
                              </select>
                              <div class="form-text">Status default adalah Aktif</div>
                            </div>
                          </div>
                        </div>

                        <?php if ($data['action'] === 'edit' && $data['ekspedisi']): ?>
                          <div class="row">
                            <div class="col-12">
                              <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                <strong>Informasi:</strong>
                                <ul class="mb-0 mt-2">
                                  <li>Dibuat: <?php echo htmlspecialchars($data['ekspedisi']['created_at'] ?? 'Tidak diketahui'); ?></li>
                                  <li>Terakhir diupdate: <?php echo htmlspecialchars($data['ekspedisi']['updated_at'] ?? 'Belum pernah'); ?></li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2 mt-4">
                          <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="mdi mdi-content-save"></i>
                            <?php echo $data['action'] === 'create' ? 'Simpan' : 'Update'; ?>
                          </button>
                          <a href="/web_scm/ekspedisi" class="btn btn-light">
                            <i class="mdi mdi-cancel"></i> Batal
                          </a>
                          <?php if ($data['action'] === 'create'): ?>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                              <i class="mdi mdi-download"></i> Import dari API
                            </button>
                          <?php endif; ?>
                        </div>
                        
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="mdi mdi-help-circle"></i> Panduan
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <h6 class="text-primary">Kode Ekspedisi</h6>
                        <p class="small text-muted">
                          Kode harus sesuai dengan yang didukung oleh Raja Ongkir API. 
                          Kode yang umum digunakan: JNE, POS, TIKI.
                        </p>
                      </div>
                      
                      <div class="mb-3">
                        <h6 class="text-primary">Nama Ekspedisi</h6>
                        <p class="small text-muted">
                          Nama yang akan ditampilkan kepada customer. 
                          Maksimal 50 karakter.
                        </p>
                      </div>
                      
                      <div class="mb-3">
                        <h6 class="text-primary">Status</h6>
                        <p class="small text-muted">
                          Status menentukan apakah ekspedisi dapat digunakan untuk pengiriman.
                        </p>
                      </div>

                      <?php if ($data['action'] === 'create'): ?>
                        <div class="alert alert-warning">
                          <i class="mdi mdi-alert"></i>
                          <small>
                            Pastikan kode ekspedisi yang dipilih belum ada dalam sistem.
                          </small>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <?php if (!empty($data['available_codes'])): ?>
                    <div class="card mt-3">
                      <div class="card-header">
                        <h5 class="card-title mb-0">
                          <i class="mdi mdi-format-list-bulleted"></i> Kode Tersedia
                        </h5>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <table class="table table-sm">
                            <thead>
                              <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($data['available_codes'] as $code): ?>
                                <tr>
                                  <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars(strtoupper($code['kode'])); ?></span>
                                  </td>
                                  <td><?php echo htmlspecialchars($code['nama']); ?></td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Import Modal -->
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Import Ekspedisi dari API</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Import semua ekspedisi yang didukung oleh Raja Ongkir API secara otomatis.</p>
          <div class="alert alert-info">
            <i class="mdi mdi-information"></i>
            <ul class="mb-0">
              <li>Ekspedisi yang sudah ada tidak akan digandakan</li>
              <li>Semua ekspedisi baru akan ditambahkan dengan status aktif</li>
              <li>Proses ini mungkin memakan waktu beberapa detik</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" onclick="importFromApi()">
            <i class="mdi mdi-download"></i> Import Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Modal (for edit mode) -->
  <?php if ($data['action'] === 'edit' && $data['ekspedisi']): ?>
    <div class="modal fade" id="detailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Ekspedisi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <td><strong>ID:</strong></td>
                    <td><?php echo htmlspecialchars($data['ekspedisi']['id']); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Kode:</strong></td>
                    <td>
                      <span class="badge bg-secondary"><?php echo htmlspecialchars(strtoupper($data['ekspedisi']['kode'])); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Nama:</strong></td>
                    <td><?php echo htmlspecialchars($data['ekspedisi']['nama']); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                      <?php if ($data['ekspedisi']['status'] === 'aktif'): ?>
                        <span class="badge bg-success">Aktif</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Nonaktif</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <td><strong>Dibuat:</strong></td>
                    <td><?php echo htmlspecialchars($data['ekspedisi']['created_at'] ?? 'Tidak diketahui'); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Diupdate:</strong></td>
                    <td><?php echo htmlspecialchars($data['ekspedisi']['updated_at'] ?? 'Belum pernah'); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Total Penggunaan:</strong></td>
                    <td><?php echo htmlspecialchars($data['ekspedisi']['usage_count'] ?? '0'); ?> kali</td>
                  </tr>
                  <tr>
                    <td><strong>Rating:</strong></td>
                    <td>
                      <?php 
                        $rating = $data['ekspedisi']['rating'] ?? 0;
                        for ($i = 1; $i <= 5; $i++) {
                          echo $i <= $rating ? '<i class="mdi mdi-star text-warning"></i>' : '<i class="mdi mdi-star-outline text-muted"></i>';
                        }
                      ?>
                      <span class="ms-1">(<?php echo number_format($rating, 1); ?>)</span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script>
    // Form validation
    document.getElementById('ekspedisiForm').addEventListener('submit', function(e) {
      const kode = document.getElementById('kode').value.trim();
      const nama = document.getElementById('nama').value.trim();
      
      if (!kode || !nama) {
        e.preventDefault();
        alert('Kode dan nama ekspedisi harus diisi');
        return;
      }
      
      if (kode.length < 2 || kode.length > 10) {
        e.preventDefault();
        alert('Kode ekspedisi harus antara 2-10 karakter');
        return;
      }
      
      if (nama.length < 2 || nama.length > 50) {
        e.preventDefault();
        alert('Nama ekspedisi harus antara 2-50 karakter');
        return;
      }
      
      // Show loading state
      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...';
    });

    // Auto-fill nama based on kode selection (for create mode)
    <?php if ($data['action'] === 'create'): ?>
      document.getElementById('kode').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const namaField = document.getElementById('nama');
        
        if (selectedOption.value && selectedOption.text.includes(' - ')) {
          const nama = selectedOption.text.split(' - ')[1];
          if (namaField.value === '' || confirm('Ganti nama ekspedisi dengan "' + nama + '"?')) {
            namaField.value = nama;
          }
        }
      });
    <?php endif; ?>

    // Character counter for nama field
    document.getElementById('nama').addEventListener('input', function() {
      const maxLength = 50;
      const currentLength = this.value.length;
      const formText = this.nextElementSibling;
      
      formText.textContent = `${currentLength}/${maxLength} karakter`;
      
      if (currentLength > maxLength - 10) {
        formText.classList.add('text-warning');
      } else {
        formText.classList.remove('text-warning');
      }
    });

    function importFromApi() {
      if (confirm('Import semua ekspedisi dari Raja Ongkir API?')) {
        window.location.href = '/web_scm/ekspedisi/import';
      }
    }

    function viewEkspedisiDetails() {
      new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>