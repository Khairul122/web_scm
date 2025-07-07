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
                    <div class="btn-wrapper">
                      <h3 class="text-primary">Manajemen Kategori</h3>
                    </div>
                  </div>
                  <div>
                    <button type="button" class="btn btn-primary" onclick="showCreateForm()">
                      <i class="icon-plus"></i> Tambah Kategori
                    </button>
                  </div>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer" style="margin-top: 15px;"></div>

                <!-- Kategori Table -->
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Data Kategori</h4>
                                    <p class="card-subtitle card-subtitle-dash">Kelola kategori produk</p>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Total Produk</th>
                                        <th>Aksi</th>
                                      </tr>
                                    </thead>
                                    <tbody id="kategoriTableBody">
                                      <tr>
                                        <td colspan="5" class="text-center">
                                          <div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span>
                                          </div>
                                          <p>Memuat data...</p>
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

  <!-- Modal Form -->
  <div class="modal fade" id="kategoriModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="kategoriForm">
          <div class="modal-body">
            <input type="hidden" id="kategoriId" name="id">
            <div class="form-group">
              <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
            </div>
            <div class="form-group">
              <label for="deskripsi">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let isEditMode = false;
    let editId = null;

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadKategori();
    });

    // Load all kategori
    async function loadKategori() {
      try {
        // Test API connection first
        const testResponse = await fetch('<?= API_BASE_URL ?>/health', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (!testResponse.ok) {
          throw new Error('Backend server tidak dapat dijangkau');
        }

        const authToken = getAuthToken();
        if (!authToken) {
          showAlert('error', 'Session berakhir, silakan login ulang');
          setTimeout(() => {
            window.location.href = '/web_scm/login';
          }, 2000);
          return;
        }

        const response = await fetch('<?= API_BASE_URL ?>/kategori', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + authToken
          }
        });

        const result = await response.json();
        
        if (response.ok && result.data) {
          displayKategori(result.data);
        } else if (response.status === 401 || response.status === 403) {
          showAlert('error', 'Session berakhir, silakan login ulang');
          setTimeout(() => {
            window.location.href = '/web_scm/login';
          }, 2000);
        } else {
          showAlert('error', result.error || 'Gagal memuat data kategori');
          document.getElementById('kategoriTableBody').innerHTML = 
            '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>';
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat memuat data: ' + error.message);
        document.getElementById('kategoriTableBody').innerHTML = 
          '<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan</td></tr>';
      }
    }

    // Display kategori data
    function displayKategori(kategori) {
      const tbody = document.getElementById('kategoriTableBody');
      
      if (kategori.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data kategori</td></tr>';
        return;
      }

      let html = '';
      kategori.forEach((item, index) => {
        html += `
          <tr>
            <td>${index + 1}</td>
            <td>${escapeHtml(item.nama_kategori)}</td>
            <td>${escapeHtml(item.deskripsi || '-')}</td>
            <td>
              <span class="badge badge-info">${item.total_products || 0} produk</span>
            </td>
            <td>
              <button class="btn btn-sm btn-warning" onclick="editKategori(${item.id})" title="Edit">
                <i class="icon-pencil"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteKategori(${item.id}, '${escapeHtml(item.nama_kategori)}')" title="Hapus">
                <i class="icon-trash"></i>
              </button>
            </td>
          </tr>
        `;
      });
      
      tbody.innerHTML = html;
    }

    // Show create form
    function showCreateForm() {
      isEditMode = false;
      editId = null;
      document.getElementById('modalTitle').textContent = 'Tambah Kategori';
      document.getElementById('submitBtn').textContent = 'Simpan';
      document.getElementById('kategoriForm').reset();
      document.getElementById('kategoriId').value = '';
      $('#kategoriModal').modal('show');
    }

    // Edit kategori
    async function editKategori(id) {
      try {
        const response = await fetch('<?= API_BASE_URL ?>/kategori/' + id, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getAuthToken()
          }
        });

        const result = await response.json();
        
        if (response.ok && result.data) {
          isEditMode = true;
          editId = id;
          document.getElementById('modalTitle').textContent = 'Edit Kategori';
          document.getElementById('submitBtn').textContent = 'Perbarui';
          document.getElementById('kategoriId').value = id;
          document.getElementById('nama_kategori').value = result.data.nama_kategori;
          document.getElementById('deskripsi').value = result.data.deskripsi || '';
          $('#kategoriModal').modal('show');
        } else {
          showAlert('error', result.error || 'Gagal memuat data kategori');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat memuat data');
      }
    }

    // Submit form
    document.getElementById('kategoriForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = {
        nama_kategori: document.getElementById('nama_kategori').value.trim(),
        deskripsi: document.getElementById('deskripsi').value.trim()
      };

      if (!formData.nama_kategori) {
        showAlert('error', 'Nama kategori wajib diisi');
        return;
      }

      const submitBtn = document.getElementById('submitBtn');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Menyimpan...';
      submitBtn.disabled = true;

      try {
        const url = isEditMode ? '<?= API_BASE_URL ?>/kategori/' + editId : '<?= API_BASE_URL ?>/kategori';
        const method = isEditMode ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getAuthToken()
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();
        
        if (response.ok) {
          $('#kategoriModal').modal('hide');
          showAlert('success', result.message || `Kategori berhasil ${isEditMode ? 'diperbarui' : 'ditambahkan'}`);
          loadKategori();
        } else {
          showAlert('error', result.error || `Gagal ${isEditMode ? 'memperbarui' : 'menambahkan'} kategori`);
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat menyimpan data');
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });

    // Delete kategori
    async function deleteKategori(id, nama) {
      if (!confirm(`Apakah Anda yakin ingin menghapus kategori "${nama}"?`)) {
        return;
      }

      try {
        const response = await fetch('<?= API_BASE_URL ?>/kategori/' + id, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getAuthToken()
          }
        });

        const result = await response.json();
        
        if (response.ok) {
          showAlert('success', result.message || 'Kategori berhasil dihapus');
          loadKategori();
        } else {
          showAlert('error', result.error || 'Gagal menghapus kategori');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat menghapus data');
      }
    }

    // Show alert
    function showAlert(type, message) {
      const alertContainer = document.getElementById('alertContainer');
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      
      alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          ${escapeHtml(message)}
          <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
          </button>
        </div>
      `;

      // Auto hide after 5 seconds
      setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
          $(alert).alert('close');
        }
      }, 5000);
    }

    // Get auth token from PHP session (via hidden input or AJAX call)
    function getAuthToken() {
      // Get token from PHP session via a dedicated endpoint
      try {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/web_scm/get-token', false); // Synchronous untuk token
        xhr.send();
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          return response.token || '';
        }
      } catch (e) {
        console.log('Failed to get token from session');
      }
      return '';
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>
</html>