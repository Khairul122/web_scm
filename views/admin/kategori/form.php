<?php 
$isEdit = $mode === 'edit';
$pageTitle = $isEdit ? 'Edit Kategori' : 'Tambah Kategori';
$formAction = $isEdit ? '?controller=Kategori&action=update&id=' . $kategori['id'] : '?controller=Kategori&action=store';
?>

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
                    <h4 class="card-title card-title-dash"><?= $pageTitle ?></h4>
                    <p class="card-subtitle card-subtitle-dash">
                      <?= $isEdit ? 'Perbarui informasi kategori' : 'Tambahkan kategori produk baru' ?>
                    </p>
                  </div>
                  <div>
                    <a href="?controller=Kategori&action=index" class="btn btn-secondary">
                      <i class="icon-arrow-left"></i> Kembali
                    </a>
                  </div>
                </div>
                
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" role="tabpanel">
                    
                    <!-- Alert Messages -->
                    <?php if (isset($error)): ?>
                      <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="icon-close me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <div class="row mt-4">
                      <div class="col-lg-8">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <form id="kategoriForm" method="POST" action="<?= $formAction ?>">
                              
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label for="nama_kategori" class="form-label">
                                      Nama Kategori <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_kategori" 
                                           name="nama_kategori" 
                                           value="<?= htmlspecialchars($kategori['nama_kategori'] ?? $data['nama_kategori'] ?? '') ?>"
                                           placeholder="Masukkan nama kategori"
                                           required>
                                    <div class="invalid-feedback"></div>
                                  </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" 
                                              id="deskripsi" 
                                              name="deskripsi" 
                                              rows="4"
                                              placeholder="Masukkan deskripsi kategori (opsional)"><?= htmlspecialchars($kategori['deskripsi'] ?? $data['deskripsi'] ?? '') ?></textarea>
                                    <div class="form-text">Deskripsi singkat tentang kategori ini</div>
                                  </div>
                                </div>
                              </div>

                              <div class="row mt-4">
                                <div class="col-md-12">
                                  <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                      <i class="icon-check me-2"></i>
                                      <span class="btn-text"><?= $isEdit ? 'Perbarui' : 'Simpan' ?></span>
                                      <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                    
                                    <a href="?controller=Kategori&action=index" class="btn btn-secondary">
                                      <i class="icon-close me-2"></i>
                                      Batal
                                    </a>
                                    
                                    <?php if ($isEdit): ?>
                                    <button type="button" class="btn btn-info" onclick="previewKategori()">
                                      <i class="icon-eye me-2"></i>
                                      Preview
                                    </button>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>

                            </form>
                          </div>
                        </div>
                      </div>

                      <!-- Sidebar Info -->
                      <div class="col-lg-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Informasi</h5>
                            
                            <?php if ($isEdit): ?>
                              <div class="mb-3">
                                <small class="text-muted d-block">ID Kategori</small>
                                <strong><?= $kategori['id'] ?></strong>
                              </div>
                              
                              <div class="mb-3">
                                <small class="text-muted d-block">Dibuat</small>
                                <strong><?= date('d M Y', strtotime($kategori['created_at'])) ?></strong>
                              </div>
                              
                              <?php if (isset($kategori['updated_at'])): ?>
                              <div class="mb-3">
                                <small class="text-muted d-block">Terakhir Diperbarui</small>
                                <strong><?= date('d M Y', strtotime($kategori['updated_at'])) ?></strong>
                              </div>
                              <?php endif; ?>
                              
                              <div class="mb-3">
                                <small class="text-muted d-block">Jumlah Produk</small>
                                <span class="badge badge-info"><?= $kategori['product_count'] ?? 0 ?> produk</span>
                              </div>
                            <?php endif; ?>

                            <hr>
                            
                            <h6>Tips:</h6>
                            <ul class="small text-muted">
                              <li>Gunakan nama kategori yang jelas dan mudah dipahami</li>
                              <li>Deskripsi membantu user memahami jenis produk dalam kategori</li>
                              <li>Nama kategori harus unik</li>
                              <?php if ($isEdit): ?>
                              <li>Hati-hati saat mengubah nama kategori yang sudah memiliki produk</li>
                              <?php endif; ?>
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

  <script>
    console.log('=== KATEGORI FORM DEBUG ===');
    console.log('User data from token');
    console.log('Is Edit:', <?= json_encode($isEdit) ?>);
    console.log('Kategori data:', <?= json_encode($kategori ?? null) ?>);

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

    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('kategoriForm');
      const submitBtn = document.getElementById('submitBtn');
      
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
          submitForm();
        }
      });
      
      document.getElementById('nama_kategori').addEventListener('input', function() {
        validateField(this);
      });
    });

    function validateForm() {
      let isValid = true;
      const namaKategori = document.getElementById('nama_kategori');
      
      if (!validateField(namaKategori)) {
        isValid = false;
      }
      
      return isValid;
    }

    function validateField(field) {
      const value = field.value.trim();
      let isValid = true;
      let message = '';

      switch (field.id) {
        case 'nama_kategori':
          if (!value) {
            isValid = false;
            message = 'Nama kategori harus diisi';
          } else if (value.length < 3) {
            isValid = false;
            message = 'Nama kategori minimal 3 karakter';
          } else if (value.length > 50) {
            isValid = false;
            message = 'Nama kategori maksimal 50 karakter';
          }
          break;
      }

      const feedback = field.parentNode.querySelector('.invalid-feedback');
      if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        feedback.textContent = '';
      } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        feedback.textContent = message;
      }

      return isValid;
    }

    async function submitForm() {
      const form = document.getElementById('kategoriForm');
      const submitBtn = document.getElementById('submitBtn');
      const btnText = submitBtn.querySelector('.btn-text');
      const spinner = submitBtn.querySelector('.spinner-border');
      
      submitBtn.disabled = true;
      btnText.style.display = 'none';
      spinner.classList.remove('d-none');
      
      try {
        const formData = new FormData(form);
        const data = {
          nama_kategori: formData.get('nama_kategori'),
          deskripsi: formData.get('deskripsi')
        };
        
        const isEdit = <?= json_encode($isEdit) ?>;
        const kategoriId = <?= json_encode($kategori['id'] ?? null) ?>;
        
        let url, method;
        if (isEdit) {
          url = `?api=kategori&path=update&id=${kategoriId}`;
          method = 'PUT';
        } else {
          url = '?api=kategori&path=create';
          method = 'POST';
        }
        
        const response = await fetch(url, {
          method: method,
          headers: getAuthHeaders(),
          body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.status === 401) {
          showAlert('Session expired. Please login again.', 'error');
          setTimeout(() => {
            window.location.href = '?controller=Auth&action=index';
          }, 2000);
          return;
        }
        
        if (result.success || response.ok) {
          showAlert(
            isEdit ? 'Kategori berhasil diperbarui!' : 'Kategori berhasil ditambahkan!', 
            'success'
          );
          
          clearDraft();
          
          setTimeout(() => {
            window.location.href = '?controller=Kategori&action=index' + 
              (isEdit ? '&success=2' : '&success=1');
          }, 1500);
        } else {
          showAlert(result.error || 'Terjadi kesalahan', 'error');
        }
        
      } catch (error) {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan jaringan', 'error');
      } finally {
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        spinner.classList.add('d-none');
      }
    }

    function showAlert(message, type) {
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      const iconClass = type === 'success' ? 'icon-check' : 'icon-close';
      
      const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          <i class="${iconClass} me-2"></i>
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      
      const form = document.getElementById('kategoriForm');
      form.insertAdjacentHTML('afterbegin', alertHtml);
      
      setTimeout(() => {
        const alert = form.querySelector('.alert');
        if (alert) {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }
      }, 5000);
    }

    function previewKategori() {
      const namaKategori = document.getElementById('nama_kategori').value;
      const deskripsi = document.getElementById('deskripsi').value;
      
      const preview = `
        <strong>Nama:</strong> ${namaKategori || 'Belum diisi'}<br>
        <strong>Deskripsi:</strong> ${deskripsi || 'Tidak ada deskripsi'}
      `;
      
      const modal = new bootstrap.Modal(document.createElement('div'));
      modal._element.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Preview Kategori</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              ${preview}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal._element);
      modal.show();
      
      modal._element.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal._element);
      });
    }

    let autoSaveTimeout;
    function autoSaveDraft() {
      clearTimeout(autoSaveTimeout);
      autoSaveTimeout = setTimeout(() => {
        const formData = {
          nama_kategori: document.getElementById('nama_kategori').value,
          deskripsi: document.getElementById('deskripsi').value
        };
        
        localStorage.setItem('kategori_draft', JSON.stringify(formData));
        console.log('Draft saved');
      }, 2000);
    }

    function loadDraft() {
      const draft = localStorage.getItem('kategori_draft');
      if (draft && !<?= json_encode($isEdit) ?>) {
        const data = JSON.parse(draft);
        if (confirm('Ditemukan draft yang belum disimpan. Muat draft?')) {
          document.getElementById('nama_kategori').value = data.nama_kategori || '';
          document.getElementById('deskripsi').value = data.deskripsi || '';
        }
      }
    }

    function clearDraft() {
      localStorage.removeItem('kategori_draft');
    }

    document.getElementById('nama_kategori').addEventListener('input', autoSaveDraft);
    document.getElementById('deskripsi').addEventListener('input', autoSaveDraft);

    loadDraft();
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>