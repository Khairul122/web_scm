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
                    <h4 class="card-title card-title-dash">Kelola Kategori</h4>
                    <p class="card-subtitle card-subtitle-dash">Manajemen kategori produk kopi</p>
                  </div>
                  <div>
                    <div class="btn-wrapper">
                      <a href="?controller=Kategori&action=create" class="btn btn-primary text-white me-0">
                        <i class="icon-plus"></i> Tambah Kategori
                      </a>
                    </div>
                  </div>
                </div>
                
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" role="tabpanel">
                    
                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>
                    
                    <!-- Search and Filter -->
                    <div class="row mt-3 mb-3">
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Cari kategori..." id="searchInput">
                          <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="icon-magnifier"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <button class="btn btn-info" onclick="refreshData()">
                          <i class="icon-refresh"></i> Refresh
                        </button>
                      </div>
                    </div>

                    <!-- Kategori Table -->
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table class="table table-striped" id="kategoriTable">
                                    <thead>
                                      <tr>
                                        <th>ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Produk</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody id="kategoriTableBody">
                                      <tr>
                                        <td colspan="6" class="text-center">
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

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteKategoriName"></strong>?</p>
          <p class="text-danger small">Pastikan tidak ada produk yang menggunakan kategori ini.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let kategoris = [];
    let deleteKategoriId = null;

    document.addEventListener('DOMContentLoaded', function() {
      loadKategoris();
      checkUrlParams();
      bindEvents();
    });

    function bindEvents() {
      document.getElementById('searchInput').addEventListener('input', filterKategoris);
      document.getElementById('searchBtn').addEventListener('click', filterKategoris);
      document.getElementById('confirmDeleteBtn').addEventListener('click', deleteKategori);
    }

    function checkUrlParams() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success') === '1') {
        showAlert('Kategori berhasil ditambahkan!', 'success');
      } else if (urlParams.get('success') === '2') {
        showAlert('Kategori berhasil diperbarui!', 'success');
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

    async function loadKategoris() {
      try {
        const response = await fetch('?api=kategori&path=list', {
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        console.log('Kategori Data:', result);
        
        if (response.status === 401) {
          showAlert('Session expired. Please login again.', 'error');
          setTimeout(() => {
            window.location.href = '?controller=Auth&action=index';
          }, 2000);
          return;
        }
        
        if (result.data && result.data.data) {
          kategoris = result.data.data;
          displayKategoris(kategoris);
        } else {
          showAlert('Gagal memuat data kategori', 'error');
        }
      } catch (error) {
        console.error('Error loading kategoris:', error);
        showAlert('Terjadi kesalahan saat memuat data', 'error');
      }
    }

    function displayKategoris(data) {
      const tbody = document.getElementById('kategoriTableBody');
      tbody.innerHTML = '';

      if (data.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center">
              <div class="py-4">
                <i class="icon-folder text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-2">Belum ada kategori</p>
                <a href="?controller=Kategori&action=create" class="btn btn-primary">
                  <i class="icon-plus"></i> Tambah Kategori Pertama
                </a>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      data.forEach(kategori => {
        const row = tbody.insertRow();
        row.innerHTML = `
          <td>${kategori.id}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="symbol symbol-40px me-3">
                <div class="symbol-label bg-light">
                  <i class="icon-tag text-primary"></i>
                </div>
              </div>
              <div>
                <strong>${kategori.nama_kategori}</strong>
              </div>
            </div>
          </td>
          <td>${kategori.deskripsi || '-'}</td>
          <td>
            <span class="badge badge-info">${kategori.product_count || 0} produk</span>
          </td>
          <td>${formatDate(kategori.created_at)}</td>
          <td>
            <div class="btn-group">
              <button class="btn btn-sm btn-info" onclick="viewKategori(${kategori.id})" title="Lihat">
                <i class="icon-eye"></i>
              </button>
              <button class="btn btn-sm btn-warning" onclick="editKategori(${kategori.id})" title="Edit">
                <i class="icon-pencil"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="confirmDelete(${kategori.id}, '${kategori.nama_kategori}')" title="Hapus">
                <i class="icon-trash"></i>
              </button>
            </div>
          </td>
        `;
      });
    }

    function filterKategoris() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const filtered = kategoris.filter(kategori => 
        kategori.nama_kategori.toLowerCase().includes(searchTerm) ||
        (kategori.deskripsi && kategori.deskripsi.toLowerCase().includes(searchTerm))
      );
      displayKategoris(filtered);
    }

    function viewKategori(id) {
      window.location.href = `?controller=Kategori&action=show&id=${id}`;
    }

    function editKategori(id) {
      window.location.href = `?controller=Kategori&action=edit&id=${id}`;
    }

    function confirmDelete(id, name) {
      deleteKategoriId = id;
      document.getElementById('deleteKategoriName').textContent = name;
      new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    async function deleteKategori() {
      if (!deleteKategoriId) return;

      try {
        const response = await fetch(`?controller=Kategori&action=delete&id=${deleteKategoriId}`, {
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
        
        if (result.message || result.success) {
          showAlert('Kategori berhasil dihapus!', 'success');
          loadKategoris();
        } else {
          showAlert(result.error || 'Gagal menghapus kategori', 'error');
        }
      } catch (error) {
        console.error('Error deleting kategori:', error);
        showAlert('Terjadi kesalahan saat menghapus', 'error');
      }

      bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
      deleteKategoriId = null;
    }

    function refreshData() {
      document.getElementById('searchInput').value = '';
      loadKategoris();
    }

    function showAlert(message, type) {
      const alertContainer = document.getElementById('alertContainer');
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      
      alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          <i class="icon-${type === 'success' ? 'check' : 'close'} me-2"></i>
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      setTimeout(() => {
        alertContainer.innerHTML = '';
      }, 5000);
    }

    function formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>