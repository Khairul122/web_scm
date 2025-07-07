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
              <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                        <?php echo isset($data['search_query']) ? 'Hasil Pencarian: "' . htmlspecialchars($data['search_query']) . '"' : 'Ekspedisi Management'; ?>
                      </a>
                    </li>
                  </ul>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#searchModal">
                      <i class="mdi mdi-magnify"></i> Cari
                    </button>
                    <div class="dropdown">
                      <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-filter"></i> Filter
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/web_scm/ekspedisi">Semua Ekspedisi</a></li>
                        <li><a class="dropdown-item" href="/web_scm/ekspedisi?status=active">Aktif</a></li>
                        <li><a class="dropdown-item" href="/web_scm/ekspedisi?status=inactive">Nonaktif</a></li>
                      </ul>
                    </div>
                    <div class="dropdown">
                      <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog"></i> Tools
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="showImportModal()">
                          <i class="mdi mdi-download"></i> Import dari API
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="showBulkUpdateModal()">
                          <i class="mdi mdi-format-list-bulleted"></i> Update Status Massal
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="showCleanupModal()">
                          <i class="mdi mdi-delete-sweep"></i> Bersihkan Performa Buruk
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/web_scm/ekspedisi/analytics">
                          <i class="mdi mdi-chart-line"></i> Analytics
                        </a></li>
                      </ul>
                    </div>
                    <a href="/web_scm/ekspedisi/create" class="btn btn-primary btn-sm text-white mb-0 me-0">
                      <i class="mdi mdi-plus"></i> Tambah Ekspedisi
                    </a>
                  </div>
                </div>
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    
                    <?php if (isset($data['error']) && !empty($data['error'])): ?>
                      <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <?php echo htmlspecialchars($data['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>

                    <?php if (isset($data['success']) && !empty($data['success'])): ?>
                      <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?php echo htmlspecialchars($data['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              <h4 class="card-title mb-0">
                                Daftar Ekspedisi
                                <?php if (isset($data['status_filter']) && !empty($data['status_filter'])): ?>
                                  <span class="badge bg-info ms-2"><?php echo ucfirst($data['status_filter']); ?></span>
                                <?php endif; ?>
                              </h4>
                              <div class="d-flex align-items-center">
                                <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                <label for="selectAll" class="form-check-label me-3">Pilih Semua</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="bulkActionBtn" style="display: none;" onclick="showBulkUpdateModal()">
                                  <i class="mdi mdi-format-list-bulleted"></i> Aksi Massal
                                </button>
                              </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-striped" id="ekspedisiTable">
                                <thead>
                                  <tr>
                                    <th width="40">
                                      <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>Kode</th>
                                    <th>Nama Ekspedisi</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php if (!empty($data['ekspedisi'])): ?>
                                    <?php foreach ($data['ekspedisi'] as $ekspedisi): ?>
                                      <tr>
                                        <td>
                                          <input type="checkbox" class="form-check-input row-checkbox" value="<?php echo $ekspedisi['id']; ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($ekspedisi['id']); ?></td>
                                        <td>
                                          <span class="badge bg-secondary"><?php echo htmlspecialchars(strtoupper($ekspedisi['kode'])); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($ekspedisi['nama']); ?></td>
                                        <td>
                                          <?php if ($ekspedisi['status'] === 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                          <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                          <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($ekspedisi['created_at'] ?? '-'); ?></td>
                                        <td>
                                          <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                              <i class="mdi mdi-cog"></i> Aksi
                                            </button>
                                            <ul class="dropdown-menu">
                                              <li>
                                                <a href="/web_scm/ekspedisi/edit/<?php echo $ekspedisi['id']; ?>" class="dropdown-item">
                                                  <i class="mdi mdi-pencil"></i> Edit
                                                </a>
                                              </li>
                                              <li>
                                                <a href="#" class="dropdown-item" onclick="toggleStatus(<?php echo $ekspedisi['id']; ?>, '<?php echo $ekspedisi['status'] === 'aktif' ? 'nonaktif' : 'aktif'; ?>')">
                                                  <i class="mdi mdi-toggle-switch"></i> 
                                                  <?php echo $ekspedisi['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                </a>
                                              </li>
                                              <li><hr class="dropdown-divider"></li>
                                              <li>
                                                <a href="#" class="dropdown-item text-danger" onclick="deleteEkspedisi(<?php echo $ekspedisi['id']; ?>)">
                                                  <i class="mdi mdi-delete"></i> Hapus
                                                </a>
                                              </li>
                                            </ul>
                                          </div>
                                        </td>
                                      </tr>
                                    <?php endforeach; ?>
                                  <?php else: ?>
                                    <tr>
                                      <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                          <i class="mdi mdi-inbox mdi-48px"></i>
                                          <p class="mt-2">Tidak ada data ekspedisi</p>
                                          <?php if (isset($data['search_query'])): ?>
                                            <a href="/web_scm/ekspedisi" class="btn btn-sm btn-primary">Kembali ke Semua Data</a>
                                          <?php endif; ?>
                                        </div>
                                      </td>
                                    </tr>
                                  <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Modal -->
  <div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cari Ekspedisi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="/web_scm/ekspedisi/search" method="GET">
            <div class="mb-3">
              <label for="searchQuery" class="form-label">Kata Kunci</label>
              <input type="text" class="form-control" id="searchQuery" name="q" placeholder="Masukkan kode atau nama ekspedisi..." required>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-magnify"></i> Cari
              </button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </form>
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
          <p>Import ekspedisi dari Raja Ongkir API. Proses ini akan menambahkan ekspedisi baru yang belum ada di database.</p>
          <div class="alert alert-info">
            <i class="mdi mdi-information"></i>
            Ekspedisi yang sudah ada tidak akan digandakan.
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

  <!-- Bulk Update Modal -->
  <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Status Massal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="bulkUpdateForm">
            <div class="mb-3">
              <label for="bulkStatus" class="form-label">Status Baru</label>
              <select class="form-select" id="bulkStatus" name="status" required>
                <option value="">Pilih Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
              </select>
            </div>
            <div class="alert alert-info">
              <i class="mdi mdi-information"></i>
              <span id="selectedCount">0</span> ekspedisi akan diperbarui.
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" onclick="executeBulkUpdate()">
            <i class="mdi mdi-check-all"></i> Update
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Cleanup Modal -->
  <div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bersihkan Performa Buruk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="cleanupForm">
            <div class="mb-3">
              <label for="threshold" class="form-label">Threshold Performa (%)</label>
              <input type="number" class="form-control" id="threshold" name="threshold" value="50" min="1" max="100" required>
              <div class="form-text">Ekspedisi dengan performa di bawah nilai ini akan dinonaktifkan.</div>
            </div>
            <div class="alert alert-warning">
              <i class="mdi mdi-alert"></i>
              Tindakan ini akan menonaktifkan ekspedisi dengan performa buruk.
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" onclick="executeCleanup()">
            <i class="mdi mdi-delete-sweep"></i> Bersihkan
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.row-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateBulkActionButton();
    });

    document.getElementById('selectAllHeader').addEventListener('change', function() {
      document.getElementById('selectAll').checked = this.checked;
      document.getElementById('selectAll').dispatchEvent(new Event('change'));
    });

    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', updateBulkActionButton);
    });

    function updateBulkActionButton() {
      const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
      const bulkActionBtn = document.getElementById('bulkActionBtn');
      
      if (selectedCheckboxes.length > 0) {
        bulkActionBtn.style.display = 'inline-block';
        document.getElementById('selectedCount').textContent = selectedCheckboxes.length;
      } else {
        bulkActionBtn.style.display = 'none';
      }
    }

    function deleteEkspedisi(id) {
      if (confirm('Apakah Anda yakin ingin menghapus ekspedisi ini?')) {
        window.location.href = '/web_scm/ekspedisi/delete/' + id;
      }
    }

    function toggleStatus(id, newStatus) {
      if (confirm('Apakah Anda yakin ingin mengubah status ekspedisi ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/web_scm/ekspedisi/updateStatus';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
      }
    }

    function showImportModal() {
      new bootstrap.Modal(document.getElementById('importModal')).show();
    }

    function showBulkUpdateModal() {
      new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
    }

    function showCleanupModal() {
      new bootstrap.Modal(document.getElementById('cleanupModal')).show();
    }

    function importFromApi() {
      if (confirm('Mulai proses import dari Raja Ongkir API?')) {
        window.location.href = '/web_scm/ekspedisi/import';
      }
    }

    function executeBulkUpdate() {
      const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
      const status = document.getElementById('bulkStatus').value;
      
      if (selectedCheckboxes.length === 0) {
        alert('Pilih minimal satu ekspedisi');
        return;
      }
      
      if (!status) {
        alert('Pilih status yang akan diupdate');
        return;
      }
      
      if (confirm(`Update status ${selectedCheckboxes.length} ekspedisi menjadi ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/web_scm/ekspedisi/bulkUpdateStatus';
        
        selectedCheckboxes.forEach(checkbox => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids[]';
          input.value = checkbox.value;
          form.appendChild(input);
        });
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
      }
    }

    function executeCleanup() {
      const threshold = document.getElementById('threshold').value;
      
      if (!threshold) {
        alert('Masukkan nilai threshold');
        return;
      }
      
      if (confirm(`Nonaktifkan semua ekspedisi dengan performa di bawah ${threshold}%?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/web_scm/ekspedisi/cleanupPoorPerformers';
        
        const thresholdInput = document.createElement('input');
        thresholdInput.type = 'hidden';
        thresholdInput.name = 'threshold';
        thresholdInput.value = threshold;
        form.appendChild(thresholdInput);
        
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>