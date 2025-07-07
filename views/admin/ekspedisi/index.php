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
                  <i class="mdi mdi-truck"></i> Ekspedisi Management
                </h3>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-success" onclick="importFromApi()">
                    <i class="mdi mdi-download"></i> Import API
                  </button>
                  <a href="/web_scm/ekspedisi/create" class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Tambah Kurir
                  </a>
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

              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                      <i class="mdi mdi-format-list-bulleted"></i> Daftar Kurir Ekspedisi
                    </h4>
                    <div class="d-flex gap-2">
                      <div class="btn-group" role="group">
                        <a href="/web_scm/ekspedisi" 
                           class="btn btn-outline-primary <?php echo empty($data['status_filter']) ? 'active' : ''; ?>">
                          Semua
                        </a>
                        <a href="/web_scm/ekspedisi?status=active" 
                           class="btn btn-outline-success <?php echo $data['status_filter'] === 'active' ? 'active' : ''; ?>">
                          Aktif
                        </a>
                      </div>
                      <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Cari kurir..." 
                               value="<?php echo htmlspecialchars($data['search_query'] ?? ''); ?>" 
                               id="searchInput" onkeypress="handleSearch(event)">
                        <button class="btn btn-outline-secondary" type="button" onclick="performSearch()">
                          <i class="mdi mdi-magnify"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="card-body">
                  <?php if (!empty($data['search_query'])): ?>
                    <div class="mb-3">
                      <span class="text-muted">
                        Hasil pencarian untuk: "<strong><?php echo htmlspecialchars($data['search_query']); ?></strong>"
                      </span>
                      <a href="/web_scm/ekspedisi" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="mdi mdi-close"></i> Clear
                      </a>
                    </div>
                  <?php endif; ?>

                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th width="5%">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                          </th>
                          <th width="10%">ID</th>
                          <th width="15%">Kode</th>
                          <th width="35%">Nama Kurir</th>
                          <th width="15%">Status</th>
                          <th width="20%">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($data['ekspedisi'])): ?>
                          <?php foreach ($data['ekspedisi'] as $kurir): ?>
                            <tr id="kurir-<?php echo $kurir['id']; ?>">
                              <td>
                                <input type="checkbox" class="kurir-checkbox" 
                                       value="<?php echo $kurir['id']; ?>" onchange="updateBulkActions()">
                              </td>
                              <td><?php echo htmlspecialchars($kurir['id']); ?></td>
                              <td>
                                <span class="badge bg-secondary">
                                  <?php echo htmlspecialchars(strtoupper($kurir['kode'])); ?>
                                </span>
                              </td>
                              <td><?php echo htmlspecialchars($kurir['nama']); ?></td>
                              <td>
                                <span class="badge <?php echo $kurir['status'] === 'aktif' ? 'bg-success' : 'bg-danger'; ?>" 
                                      id="status-<?php echo $kurir['id']; ?>">
                                  <i class="mdi <?php echo $kurir['status'] === 'aktif' ? 'mdi-check-circle' : 'mdi-close-circle'; ?>"></i>
                                  <?php echo $kurir['status'] === 'aktif' ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                              </td>
                              <td>
                                <div class="btn-group" role="group">
                                  <a href="/web_scm/ekspedisi/edit/<?php echo $kurir['id']; ?>" 
                                     class="btn btn-sm btn-warning" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                  </a>
                                  
                                  <button onclick="toggleStatus(<?php echo $kurir['id']; ?>, '<?php echo $kurir['status']; ?>')" 
                                          class="btn btn-sm <?php echo $kurir['status'] === 'aktif' ? 'btn-secondary' : 'btn-success'; ?>" 
                                          id="toggle-btn-<?php echo $kurir['id']; ?>"
                                          title="<?php echo $kurir['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                    <i class="mdi <?php echo $kurir['status'] === 'aktif' ? 'mdi-pause' : 'mdi-play'; ?>" 
                                       id="toggle-icon-<?php echo $kurir['id']; ?>"></i>
                                  </button>
                                  
                                  <button onclick="deleteKurir(<?php echo $kurir['id']; ?>, '<?php echo htmlspecialchars($kurir['nama']); ?>')" 
                                          class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="mdi mdi-delete"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="6" class="text-center py-5">
                              <div class="text-center">
                                <i class="mdi mdi-inbox mdi-48px text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data kurir</h5>
                                <p class="text-muted">Silahkan tambah kurir baru atau import dari API</p>
                                <a href="/web_scm/ekspedisi/create" class="btn btn-primary">
                                  <i class="mdi mdi-plus"></i> Tambah Kurir
                                </a>
                              </div>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>

                  <?php if (!empty($data['ekspedisi'])): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div id="bulkActions" style="display: none;">
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('aktif')">
                            <i class="mdi mdi-check"></i> Aktifkan Terpilih
                          </button>
                          <button type="button" class="btn btn-secondary btn-sm" onclick="bulkUpdateStatus('nonaktif')">
                            <i class="mdi mdi-close"></i> Nonaktifkan Terpilih
                          </button>
                        </div>
                      </div>
                      <div>
                        <small class="text-muted">
                          Total: <?php echo count($data['ekspedisi']); ?> kurir
                        </small>
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

  <!-- Hidden Forms -->
  <form id="statusForm" method="POST" action="/web_scm/ekspedisi/updateStatus" style="display: none;">
    <input type="hidden" name="id" id="statusKurirId">
    <input type="hidden" name="status" id="statusValue">
  </form>

  <form id="deleteForm" method="POST" action="/web_scm/ekspedisi/delete" style="display: none;">
    <input type="hidden" name="id" id="deleteKurirId">
  </form>

  <form id="bulkForm" method="POST" action="/web_scm/ekspedisi/bulkUpdateStatus" style="display: none;">
    <input type="hidden" name="status" id="bulkStatus">
    <div id="bulkIds"></div>
  </form>

  <script>
    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.kurir-checkbox');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
      });
      
      updateBulkActions();
    }

    function updateBulkActions() {
      const checkedBoxes = document.querySelectorAll('.kurir-checkbox:checked');
      const bulkActions = document.getElementById('bulkActions');
      
      if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
      } else {
        bulkActions.style.display = 'none';
      }
    }

    function toggleStatus(id, currentStatus) {
      const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
      const confirmMessage = currentStatus === 'aktif' ? 'Nonaktifkan kurir ini?' : 'Aktifkan kurir ini?';
      
      if (confirm(confirmMessage)) {
        document.getElementById('statusKurirId').value = id;
        document.getElementById('statusValue').value = newStatus;
        document.getElementById('statusForm').submit();
      }
    }

    function deleteKurir(id, nama) {
      if (confirm(`Apakah Anda yakin ingin menghapus kurir "${nama}"? Data yang sudah dihapus tidak dapat dikembalikan.`)) {
        document.getElementById('deleteKurirId').value = id;
        document.getElementById('deleteForm').submit();
      }
    }

    function bulkUpdateStatus(status) {
      const checkedBoxes = document.querySelectorAll('.kurir-checkbox:checked');
      
      if (checkedBoxes.length === 0) {
        alert('Pilih minimal satu kurir untuk diperbarui statusnya.');
        return;
      }
      
      if (confirm(`Apakah Anda yakin ingin mengubah status ${checkedBoxes.length} kurir terpilih menjadi ${status}?`)) {
        document.getElementById('bulkStatus').value = status;
        
        const bulkIds = document.getElementById('bulkIds');
        bulkIds.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids[]';
          input.value = checkbox.value;
          bulkIds.appendChild(input);
        });
        
        document.getElementById('bulkForm').submit();
      }
    }

    function importFromApi() {
      if (confirm('Apakah Anda yakin ingin mengimpor data kurir dari Raja Ongkir API? Data yang sudah ada akan diperbarui.')) {
        window.location.href = '/web_scm/ekspedisi/import';
      }
    }

    function handleSearch(event) {
      if (event.key === 'Enter') {
        performSearch();
      }
    }

    function performSearch() {
      const query = document.getElementById('searchInput').value.trim();
      if (query) {
        window.location.href = `/web_scm/ekspedisi?q=${encodeURIComponent(query)}`;
      } else {
        window.location.href = '/web_scm/ekspedisi';
      }
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          if (alert.parentNode) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
              if (alert.parentNode) {
                alert.remove();
              }
            }, 500);
          }
        }, 5000);
      });
    });
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>