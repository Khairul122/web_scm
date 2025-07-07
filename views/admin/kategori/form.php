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
                      <h3 class="text-primary" id="pageTitle">Form Kategori</h3>
                    </div>
                  </div>
                  <div>
                    <a href="/web_scm/admin/kategori" class="btn btn-secondary">
                      <i class="icon-arrow-left"></i> Kembali
                    </a>
                  </div>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer" style="margin-top: 15px;"></div>

                <!-- Form -->
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                      <div class="col-lg-8 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <form id="kategoriForm">
                                  <input type="hidden" id="kategoriId" name="id">
                                  
                                  <div class="form-group">
                                    <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                                  </div>

                                  <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Masukkan deskripsi kategori (opsional)"></textarea>
                                  </div>

                                  <div id="productInfo" class="form-group" style="display: none;">
                                    <label>Total Produk</label>
                                    <div class="form-control-plaintext">
                                      <span id="totalProducts" class="badge badge-info"></span>
                                    </div>
                                  </div>

                                  <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                      <i class="icon-check"></i> Simpan
                                    </button>
                                    <a href="/web_scm/admin/kategori" class="btn btn-secondary ml-2">
                                      <i class="icon-close"></i> Batal
                                    </a>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-lg-4 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <h5 class="card-title">Informasi</h5>
                                <ul class="list-unstyled">
                                  <li><i class="icon-info text-info"></i> Nama kategori harus unik</li>
                                  <li><i class="icon-info text-info"></i> Deskripsi bersifat opsional</li>
                                  <li><i class="icon-info text-info"></i> Kategori yang memiliki produk tidak dapat dihapus</li>
                                </ul>
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

  <script>
    let isEditMode = false;
    let editId = null;

    // Check if we're in edit mode
    document.addEventListener('DOMContentLoaded', function() {
      const pathSegments = window.location.pathname.split('/');
      const editIndex = pathSegments.indexOf('edit');
      
      if (editIndex !== -1 && pathSegments[editIndex + 1]) {
        editId = pathSegments[editIndex + 1];
        isEditMode = true;
        document.getElementById('pageTitle').textContent = 'Edit Kategori';
        document.getElementById('submitBtn').innerHTML = '<i class="icon-check"></i> Perbarui';
        loadKategoriData(editId);
      } else {
        document.getElementById('pageTitle').textContent = 'Tambah Kategori';
        document.getElementById('submitBtn').innerHTML = '<i class="icon-check"></i> Simpan';
      }
    });

    // Load kategori data for edit
    async function loadKategoriData(id) {
      try {
        showLoading(true);
        
        const response = await fetch(`/api/kategori/${id}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getAuthToken()
          }
        });

        const result = await response.json();
        
        if (response.ok && result.data) {
          document.getElementById('kategoriId').value = id;
          document.getElementById('nama_kategori').value = result.data.nama_kategori;
          document.getElementById('deskripsi').value = result.data.deskripsi || '';
          
          // Show product info if editing
          if (result.data.total_products !== undefined) {
            document.getElementById('totalProducts').textContent = `${result.data.total_products} produk`;
            document.getElementById('productInfo').style.display = 'block';
          }
        } else {
          showAlert('error', result.error || 'Gagal memuat data kategori');
          setTimeout(() => {
            window.location.href = '/web_scm/admin/kategori';
          }, 2000);
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat memuat data');
        setTimeout(() => {
          window.location.href = '/web_scm/admin/kategori';
        }, 2000);
      } finally {
        showLoading(false);
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
        document.getElementById('nama_kategori').focus();
        return;
      }

      const submitBtn = document.getElementById('submitBtn');
      const originalHtml = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="icon-spinner"></i> Menyimpan...';
      submitBtn.disabled = true;

      try {
        const url = isEditMode ? `/api/kategori/${editId}` : '/api/kategori';
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
          showAlert('success', result.message || `Kategori berhasil ${isEditMode ? 'diperbarui' : 'ditambahkan'}`);
          setTimeout(() => {
            window.location.href = '/web_scm/admin/kategori';
          }, 1500);
        } else {
          showAlert('error', result.error || `Gagal ${isEditMode ? 'memperbarui' : 'menambahkan'} kategori`);
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat menyimpan data');
      } finally {
        submitBtn.innerHTML = originalHtml;
        submitBtn.disabled = false;
      }
    });

    // Show loading state
    function showLoading(show) {
      const form = document.getElementById('kategoriForm');
      const inputs = form.querySelectorAll('input, textarea, button');
      
      inputs.forEach(input => {
        input.disabled = show;
      });

      if (show) {
        document.getElementById('submitBtn').innerHTML = '<i class="icon-spinner"></i> Loading...';
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

      // Auto hide success messages after 3 seconds
      if (type === 'success') {
        setTimeout(() => {
          const alert = alertContainer.querySelector('.alert');
          if (alert) {
            $(alert).alert('close');
          }
        }, 3000);
      }
    }

    // Get auth token from session/localStorage
    function getAuthToken() {
      // Adjust this based on your auth implementation
      return sessionStorage.getItem('api_token') || localStorage.getItem('api_token') || '';
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