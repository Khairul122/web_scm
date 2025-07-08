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
                  <div>
                    <h4 class="card-title card-title-dash">Kelola Ekspedisi</h4>
                    <p class="card-subtitle card-subtitle-dash">Manajemen layanan ekspedisi dan kurir</p>
                  </div>
                  <div>
                    <div class="btn-wrapper">
                      <button class="btn btn-info me-2" onclick="importFromApi()">
                        <i class="icon-cloud-download"></i> Import API
                      </button>
                      <button class="btn btn-success me-2" onclick="showStats()">
                        <i class="icon-graph"></i> Statistik
                      </button>
                      <a href="?controller=Ekspedisi&action=create" class="btn btn-primary text-white me-0">
                        <i class="icon-plus"></i> Tambah Ekspedisi
                      </a>
                    </div>
                  </div>
                </div>
                
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" role="tabpanel">
                    
                    <div id="alertContainer"></div>
                    
                    <div class="row mt-3 mb-3">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Cari ekspedisi..." id="searchInput">
                          <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="icon-magnifier"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                          <option value="">Semua Status</option>
                          <option value="aktif">Aktif</option>
                          <option value="nonaktif">Non-aktif</option>
                        </select>
                      </div>
                      <div class="col-md-5">
                        <button class="btn btn-info me-2" onclick="refreshData()">
                          <i class="icon-refresh"></i> Refresh
                        </button>
                        <button class="btn btn-warning me-2" onclick="showBulkUpdate()">
                          <i class="icon-settings"></i> Bulk Update
                        </button>
                        <button class="btn btn-secondary" onclick="exportData()">
                          <i class="icon-doc"></i> Export
                        </button>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table class="table table-striped" id="ekspedisiTable">
                                    <thead>
                                      <tr>
                                        <th>
                                          <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Kode</th>
                                        <th>Nama Ekspedisi</th>
                                        <th>Status</th>
                                        <th>Jumlah Pengiriman</th>
                                        <th>Rating</th>
                                        <th>Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody id="ekspedisiTableBody">
                                      <tr>
                                        <td colspan="7" class="text-center">
                                          <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                          </div>
                                        </td>
                                      </tr>
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
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus ekspedisi <strong id="deleteEkspedisiName"></strong>?</p>
          <p class="text-danger small">Data pengiriman yang menggunakan ekspedisi ini akan terpengaruh.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Status Massal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Pilih status baru untuk <span id="selectedCount">0</span> ekspedisi yang dipilih:</p>
          <div class="form-group">
            <label>Status Baru:</label>
            <select class="form-select" id="bulkStatus">
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Non-aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="confirmBulkUpdateBtn">Update</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Statistik Ekspedisi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="statsContent">
          <div class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let ekspedisis = [];
    let deleteEkspedisiId = null;
    let selectedIds = [];

    document.addEventListener('DOMContentLoaded', function() {
      loadEkspedisis();
      checkUrlParams();
      bindEvents();
    });

    function bindEvents() {
      document.getElementById('searchInput').addEventListener('input', filterEkspedisis);
      document.getElementById('searchBtn').addEventListener('click', filterEkspedisis);
      document.getElementById('statusFilter').addEventListener('change', filterEkspedisis);
      document.getElementById('confirmDeleteBtn').addEventListener('click', deleteEkspedisi);
      document.getElementById('confirmBulkUpdateBtn').addEventListener('click', bulkUpdateStatus);
      document.getElementById('selectAll').addEventListener('change', toggleSelectAll);
    }

    function checkUrlParams() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success') === '1') {
        showAlert('Ekspedisi berhasil ditambahkan!', 'success');
      } else if (urlParams.get('success') === '2') {
        showAlert('Ekspedisi berhasil diperbarui!', 'success');
      } else if (urlParams.get('error') === '1') {
        showAlert('Terjadi kesalahan!', 'error');
      }
    }

    function getAuthHeaders() {
      const token = getCookie('auth_token');
      if (token) {
        return {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        };
      }
      return { 'Content-Type': 'application/json' };
    }

    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }

    async function loadEkspedisis() {
      try {
        const response = await fetch('?api=ekspedisi&path=list', {
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        console.log('Ekspedisi Data:', result);
        
        if (response.status === 401) {
          showAlert('Session expired. Please login again.', 'error');
          setTimeout(() => {
            window.location.href = '?controller=Auth&action=index';
          }, 2000);
          return;
        }
        
        if (result.success && result.data && result.data.data) {
          ekspedisis = result.data.data;
          displayEkspedisis(ekspedisis);
        } else if (result.data && result.data.data) {
          ekspedisis = result.data.data;
          displayEkspedisis(ekspedisis);
        } else {
          showAlert('Gagal memuat data ekspedisi', 'error');
        }
      } catch (error) {
        console.error('Error loading ekspedisis:', error);
        showAlert('Terjadi kesalahan saat memuat data', 'error');
      }
    }

    function displayEkspedisis(data) {
      const tbody = document.getElementById('ekspedisiTableBody');
      tbody.innerHTML = '';

      if (!data || data.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="7" class="text-center">
              <div class="py-4">
                <i class="icon-plane text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-2">Belum ada ekspedisi</p>
                <a href="?controller=Ekspedisi&action=create" class="btn btn-primary">
                  <i class="icon-plus"></i> Tambah Ekspedisi Pertama
                </a>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      data.forEach(ekspedisi => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>
            <input type="checkbox" class="form-check-input row-checkbox" value="${ekspedisi.id}">
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="symbol symbol-40px me-3">
                <div class="symbol-label bg-light">
                  <i class="icon-plane text-primary"></i>
                </div>
              </div>
              <strong>${ekspedisi.kode ? ekspedisi.kode.toUpperCase() : ''}</strong>
            </div>
          </td>
          <td>${ekspedisi.nama || ''}</td>
          <td>
            <span class="badge badge-${ekspedisi.status === 'aktif' ? 'success' : 'secondary'}">
              ${ekspedisi.status === 'aktif' ? 'Aktif' : 'Non-aktif'}
            </span>
          </td>
          <td>
            <span class="badge badge-info">${ekspedisi.total_deliveries || 0} pengiriman</span>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <span class="me-2">${ekspedisi.avg_rating || 0}/5</span>
              <div class="progress" style="width: 60px; height: 8px;">
                <div class="progress-bar bg-warning" style="width: ${(ekspedisi.avg_rating || 0) * 20}%"></div>
              </div>
            </div>
          </td>
          <td>
            <div class="btn-group">
              <button class="btn btn-sm btn-info" onclick="viewEkspedisi(${ekspedisi.id})" title="Lihat">
                <i class="icon-eye"></i>
              </button>
              <button class="btn btn-sm btn-warning" onclick="editEkspedisi(${ekspedisi.id})" title="Edit">
                <i class="icon-pencil"></i>
              </button>
              <button class="btn btn-sm ${ekspedisi.status === 'aktif' ? 'btn-secondary' : 'btn-success'}" 
                      onclick="toggleStatus(${ekspedisi.id}, '${ekspedisi.status}')" 
                      title="${ekspedisi.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}">
                <i class="icon-${ekspedisi.status === 'aktif' ? 'close' : 'check'}"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="confirmDelete(${ekspedisi.id}, '${ekspedisi.nama}')" title="Hapus">
                <i class="icon-trash"></i>
              </button>
            </div>
          </td>
        `;
      });

      document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedIds);
      });
    }

    function filterEkspedisis() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      
      const filtered = ekspedisis.filter(ekspedisi => {
        const matchSearch = (ekspedisi.nama && ekspedisi.nama.toLowerCase().includes(searchTerm)) ||
                           (ekspedisi.kode && ekspedisi.kode.toLowerCase().includes(searchTerm));
        const matchStatus = !statusFilter || ekspedisi.status === statusFilter;
        
        return matchSearch && matchStatus;
      });
      
      displayEkspedisis(filtered);
    }

    function viewEkspedisi(id) {
      window.location.href = `?controller=Ekspedisi&action=show&id=${id}`;
    }

    function editEkspedisi(id) {
      window.location.href = `?controller=Ekspedisi&action=edit&id=${id}`;
    }

    async function toggleStatus(id, currentStatus) {
      const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
      
      try {
        const response = await fetch(`?api=ekspedisi&path=status&id=${id}`, {
          method: 'PATCH',
          headers: getAuthHeaders(),
          body: JSON.stringify({ 
            status: newStatus 
          })
        });
        
        const result = await response.json();
        
        if (result.success) {
          showAlert(`Status ekspedisi berhasil diubah menjadi ${newStatus}!`, 'success');
          loadEkspedisis();
        } else {
          showAlert(result.error || 'Gagal mengubah status', 'error');
        }
      } catch (error) {
        console.error('Error toggling status:', error);
        showAlert('Terjadi kesalahan saat mengubah status', 'error');
      }
    }

    function confirmDelete(id, name) {
      deleteEkspedisiId = id;
      document.getElementById('deleteEkspedisiName').textContent = name;
      new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    async function deleteEkspedisi() {
      if (!deleteEkspedisiId) return;

      try {
        const response = await fetch(`?api=ekspedisi&path=delete&id=${deleteEkspedisiId}`, {
          method: 'DELETE',
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        if (response.status === 401) {
          showAlert('Session expired. Please login again.', 'error');
          setTimeout(() => {
            window.location.href = '?controller=Auth&action=index';
          }, 2000);
          return;
        }
        
        if (result.success) {
          showAlert('Ekspedisi berhasil dihapus!', 'success');
          loadEkspedisis();
        } else {
          showAlert(result.error || 'Gagal menghapus ekspedisi', 'error');
        }
      } catch (error) {
        console.error('Error deleting ekspedisi:', error);
        showAlert('Terjadi kesalahan saat menghapus', 'error');
      }

      bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
      deleteEkspedisiId = null;
    }

    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.row-checkbox');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
      });
      
      updateSelectedIds();
    }

    function updateSelectedIds() {
      selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                        .map(checkbox => parseInt(checkbox.value));
      
      document.getElementById('selectedCount').textContent = selectedIds.length;
    }

    function showBulkUpdate() {
      updateSelectedIds();
      
      if (selectedIds.length === 0) {
        showAlert('Pilih minimal satu ekspedisi untuk diupdate', 'warning');
        return;
      }
      
      new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
    }

    async function bulkUpdateStatus() {
      const status = document.getElementById('bulkStatus').value;
      
      try {
        const response = await fetch('?api=ekspedisi&path=bulk-update', {
          method: 'POST',
          headers: getAuthHeaders(),
          body: JSON.stringify({
            ids: selectedIds,
            status: status
          })
        });
        
        const result = await response.json();
        
        if (result.success) {
          showAlert(`${result.data.updated_count || selectedIds.length} ekspedisi berhasil diupdate!`, 'success');
          loadEkspedisis();
          selectedIds = [];
          document.getElementById('selectAll').checked = false;
        } else {
          showAlert(result.error || 'Gagal melakukan bulk update', 'error');
        }
      } catch (error) {
        console.error('Error bulk updating:', error);
        showAlert('Terjadi kesalahan saat bulk update', 'error');
      }
      
      bootstrap.Modal.getInstance(document.getElementById('bulkUpdateModal')).hide();
    }

    async function showStats() {
      const modal = new bootstrap.Modal(document.getElementById('statsModal'));
      modal.show();
      
      try {
        const response = await fetch('?api=ekspedisi&path=stats', {
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        if (result.success && result.data) {
          const stats = result.data.data || result.data;
          document.getElementById('statsContent').innerHTML = `
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body text-center">
                    <h5>Total Ekspedisi</h5>
                    <h2 class="text-primary">${stats.total || 0}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body text-center">
                    <h5>Ekspedisi Aktif</h5>
                    <h2 class="text-success">${stats.active || 0}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body text-center">
                    <h5>Total Pengiriman</h5>
                    <h2 class="text-info">${stats.total_deliveries || 0}</h2>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body text-center">
                    <h5>Rating Rata-rata</h5>
                    <h2 class="text-warning">${stats.avg_rating || 0}/5</h2>
                  </div>
                </div>
              </div>
            </div>
          `;
        } else {
          document.getElementById('statsContent').innerHTML = '<p class="text-danger">Gagal memuat statistik</p>';
        }
      } catch (error) {
        document.getElementById('statsContent').innerHTML = '<p class="text-danger">Gagal memuat statistik</p>';
      }
    }

    async function importFromApi() {
      if (!confirm('Import ekspedisi dari Raja Ongkir API? Ini akan menambahkan ekspedisi yang belum ada.')) {
        return;
      }
      
      try {
        const response = await fetch('?api=ekspedisi&path=import', {
          method: 'POST',
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        if (result.success) {
          showAlert(`Berhasil import ${result.data.imported_count || 0} ekspedisi!`, 'success');
          loadEkspedisis();
        } else {
          showAlert(result.message || 'Gagal melakukan import', 'error');
        }
      } catch (error) {
        console.error('Error importing:', error);
        showAlert('Terjadi kesalahan saat import', 'error');
      }
    }

    function exportData() {
      if (!ekspedisis || ekspedisis.length === 0) {
        showAlert('Tidak ada data untuk diexport', 'warning');
        return;
      }
      
      const csvContent = "data:text/csv;charset=utf-8," 
        + "Kode,Nama,Status,Total Pengiriman,Rating\n"
        + ekspedisis.map(e => `${e.kode || ''},${e.nama || ''},${e.status || ''},${e.total_deliveries || 0},${e.avg_rating || 0}`).join("\n");

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "ekspedisi_data.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function refreshData() {
      document.getElementById('searchInput').value = '';
      document.getElementById('statusFilter').value = '';
      loadEkspedisis();
    }

    function showAlert(message, type) {
      const alertContainer = document.getElementById('alertContainer');
      const alertClass = type === 'success' ? 'alert-success' : 
                        type === 'warning' ? 'alert-warning' : 'alert-danger';
      
      alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          <i class="icon-${type === 'success' ? 'check' : type === 'warning' ? 'exclamation' : 'close'} me-2"></i>
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      setTimeout(() => {
        alertContainer.innerHTML = '';
      }, 5000);
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>