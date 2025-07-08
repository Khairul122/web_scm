<?php 
$isEdit = $mode === 'edit';
$pageTitle = $isEdit ? 'Edit Ekspedisi' : 'Tambah Ekspedisi';
$formAction = $isEdit ? '?controller=Ekspedisi&action=update&id=' . $ekspedisi['id'] : '?controller=Ekspedisi&action=store';
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
                      <?= $isEdit ? 'Perbarui informasi ekspedisi' : 'Tambahkan layanan ekspedisi baru' ?>
                    </p>
                  </div>
                  <div>
                    <a href="?controller=Ekspedisi&action=index" class="btn btn-secondary">
                      <i class="icon-arrow-left"></i> Kembali
                    </a>
                  </div>
                </div>
                
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" role="tabpanel">
                    
                    <?php if (isset($error)): ?>
                      <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="icon-close me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                      <div class="col-lg-8">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <form id="ekspedisiForm" method="POST" action="<?= $formAction ?>">
                              
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="kode" class="form-label">
                                      Kode Ekspedisi <span class="text-danger">*</span>
                                    </label>
                                    <?php if ($isEdit): ?>
                                      <input type="text" 
                                             class="form-control" 
                                             id="kode" 
                                             name="kode" 
                                             value="<?= htmlspecialchars($ekspedisi['kode'] ?? '') ?>"
                                             readonly>
                                      <div class="form-text">Kode tidak dapat diubah setelah dibuat</div>
                                    <?php else: ?>
                                      <select class="form-select" 
                                              id="kode" 
                                              name="kode" 
                                              required>
                                        <option value="">Pilih kode ekspedisi</option>
                                        <?php if (!empty($availableCodes)): ?>
                                          <?php foreach ($availableCodes as $code): ?>
                                            <option value="<?= $code['kode'] ?>" 
                                                    <?= (isset($data['kode']) && $data['kode'] === $code['kode']) ? 'selected' : '' ?>>
                                              <?= $code['kode'] ?> - <?= $code['nama'] ?>
                                            </option>
                                          <?php endforeach; ?>
                                        <?php endif; ?>
                                      </select>
                                    <?php endif; ?>
                                    <div class="invalid-feedback"></div>
                                  </div>
                                </div>
                                
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                      <option value="aktif" <?= (!isset($ekspedisi['status']) || $ekspedisi['status'] === 'aktif' || (isset($data['status']) && $data['status'] === 'aktif')) ? 'selected' : '' ?>>
                                        Aktif
                                      </option>
                                      <option value="nonaktif" <?= (isset($ekspedisi['status']) && $ekspedisi['status'] === 'nonaktif') || (isset($data['status']) && $data['status'] === 'nonaktif') ? 'selected' : '' ?>>
                                        Non-aktif
                                      </option>
                                    </select>
                                  </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label for="nama" class="form-label">
                                      Nama Ekspedisi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= htmlspecialchars($ekspedisi['nama'] ?? $data['nama'] ?? '') ?>"
                                           placeholder="Masukkan nama ekspedisi"
                                           required>
                                    <div class="invalid-feedback"></div>
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
                                    
                                    <a href="?controller=Ekspedisi&action=index" class="btn btn-secondary">
                                      <i class="icon-close me-2"></i>
                                      Batal
                                    </a>
                                    
                                    <?php if (!$isEdit): ?>
                                    <button type="button" class="btn btn-info" onclick="loadAvailableCodes()">
                                      <i class="icon-refresh me-2"></i>
                                      Refresh Kode
                                    </button>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>

                            </form>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Informasi</h5>
                            
                            <?php if ($isEdit): ?>
                              <div class="mb-3">
                                <small class="text-muted d-block">ID Ekspedisi</small>
                                <strong><?= $ekspedisi['id'] ?></strong>
                              </div>
                              
                              <div class="mb-3">
                                <small class="text-muted d-block">Total Pengiriman</small>
                                <span class="badge badge-info"><?= $ekspedisi['total_deliveries'] ?? 0 ?> pengiriman</span>
                              </div>
                              
                              <div class="mb-3">
                                <small class="text-muted d-block">Rating Rata-rata</small>
                                <div class="d-flex align-items-center">
                                  <span class="me-2"><?= $ekspedisi['avg_rating'] ?? 0 ?>/5</span>
                                  <div class="progress" style="width: 80px; height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: <?= ($ekspedisi['avg_rating'] ?? 0) * 20 ?>%"></div>
                                  </div>
                                </div>
                              </div>
                              
                              <?php if (isset($ekspedisi['created_at'])): ?>
                              <div class="mb-3">
                                <small class="text-muted d-block">Dibuat</small>
                                <strong><?= date('d M Y', strtotime($ekspedisi['created_at'])) ?></strong>
                              </div>
                              <?php endif; ?>
                              
                              <?php if (isset($ekspedisi['updated_at'])): ?>
                              <div class="mb-3">
                                <small class="text-muted d-block">Terakhir Diperbarui</small>
                                <strong><?= date('d M Y', strtotime($ekspedisi['updated_at'])) ?></strong>
                              </div>
                              <?php endif; ?>
                            <?php endif; ?>

                            <hr>
                            
                            <h6>Tips:</h6>
                            <ul class="small text-muted">
                              <li>Pilih kode ekspedisi yang didukung oleh Raja Ongkir API</li>
                              <li>Nama ekspedisi harus jelas dan mudah dikenali</li>
                              <li>Status aktif berarti ekspedisi tersedia untuk pengiriman</li>
                              <?php if ($isEdit): ?>
                              <li>Hati-hati mengubah status jika ada pengiriman aktif</li>
                              <?php endif; ?>
                            </ul>
                            
                            <?php if (!$isEdit && !empty($availableCodes)): ?>
                            <div class="mt-3">
                              <h6>Kode Tersedia:</h6>
                              <div class="small" id="availableCodesList">
                                <?php foreach (array_slice($availableCodes, 0, 5) as $code): ?>
                                  <div class="mb-1">
                                    <span class="badge badge-light"><?= $code['kode'] ?></span>
                                    <?= $code['nama'] ?>
                                  </div>
                                <?php endforeach; ?>
                                <?php if (count($availableCodes) > 5): ?>
                                  <small class="text-muted">dan <?= count($availableCodes) - 5 ?> lainnya...</small>
                                <?php endif; ?>
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
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    console.log('=== EKSPEDISI FORM DEBUG ===');
    console.log('Is Edit:', <?= json_encode($isEdit) ?>);
    console.log('Ekspedisi data:', <?= json_encode($ekspedisi ?? null) ?>);
    console.log('Available codes:', <?= json_encode($availableCodes ?? []) ?>);

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
      const form = document.getElementById('ekspedisiForm');
      const submitBtn = document.getElementById('submitBtn');
      
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
          submitForm();
        }
      });
      
      document.getElementById('kode').addEventListener('change', function() {
        if (!<?= json_encode($isEdit) ?>) {
          autoFillNama();
        }
        validateField(this);
      });
      
      document.getElementById('nama').addEventListener('input', function() {
        validateField(this);
      });
    });

    function validateForm() {
      let isValid = true;
      const kode = document.getElementById('kode');
      const nama = document.getElementById('nama');
      
      if (!validateField(kode)) {
        isValid = false;
      }
      
      if (!validateField(nama)) {
        isValid = false;
      }
      
      return isValid;
    }

    function validateField(field) {
      const value = field.value.trim();
      let isValid = true;
      let message = '';

      switch (field.id) {
        case 'kode':
          if (!value) {
            isValid = false;
            message = 'Kode ekspedisi harus dipilih';
          }
          break;
          
        case 'nama':
          if (!value) {
            isValid = false;
            message = 'Nama ekspedisi harus diisi';
          } else if (value.length < 2) {
            isValid = false;
            message = 'Nama ekspedisi minimal 2 karakter';
          } else if (value.length > 50) {
            isValid = false;
            message = 'Nama ekspedisi maksimal 50 karakter';
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

    function autoFillNama() {
      const kodeSelect = document.getElementById('kode');
      const namaInput = document.getElementById('nama');
      const selectedOption = kodeSelect.options[kodeSelect.selectedIndex];
      
      if (selectedOption.value && selectedOption.text.includes(' - ')) {
        const namaFromOption = selectedOption.text.split(' - ')[1];
        if (!namaInput.value || confirm('Auto-fill nama ekspedisi dengan "' + namaFromOption + '"?')) {
          namaInput.value = namaFromOption;
          validateField(namaInput);
        }
      }
    }

    async function submitForm() {
      const form = document.getElementById('ekspedisiForm');
      const submitBtn = document.getElementById('submitBtn');
      const btnText = submitBtn.querySelector('.btn-text');
      const spinner = submitBtn.querySelector('.spinner-border');
      
      submitBtn.disabled = true;
      btnText.style.display = 'none';
      spinner.classList.remove('d-none');
      
      try {
        const formData = new FormData(form);
        const data = {
          kode: formData.get('kode'),
          nama: formData.get('nama'),
          status: formData.get('status') || 'aktif'
        };
        
        console.log('=== SUBMIT FORM DEBUG ===');
        console.log('Form data:', data);
        console.log('Auth token:', getCookie('auth_token') ? 'EXISTS' : 'MISSING');
        
        const isEdit = <?= json_encode($isEdit) ?>;
        const ekspedisiId = <?= json_encode($ekspedisi['id'] ?? null) ?>;
        
        let url, method;
        if (isEdit) {
          url = `?api=ekspedisi&path=update&id=${ekspedisiId}`;
          method = 'PUT';
        } else {
          url = '?api=ekspedisi&path=create';
          method = 'POST';
        }
        
        console.log('Request URL:', url);
        console.log('Request method:', method);
        console.log('Request data:', JSON.stringify(data));
        
        const headers = getAuthHeaders();
        console.log('Request headers:', headers);
        
        const response = await fetch(url, {
          method: method,
          headers: headers,
          body: JSON.stringify(data)
        });
        
        console.log('Response status:', response.status);
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        let result;
        try {
          result = JSON.parse(responseText);
        } catch (e) {
          console.error('Failed to parse response as JSON:', e);
          showAlert('Server returned invalid response: ' + responseText, 'error');
          return;
        }
        
        console.log('Parsed result:', result);
        
        if (response.status === 401) {
          showAlert('Session expired. Please login again.', 'error');
          setTimeout(() => {
            window.location.href = '?controller=Auth&action=index';
          }, 2000);
          return;
        }
        
        if (response.status === 400) {
          console.error('Bad Request Error:', result);
          showAlert(result.error || result.message || 'Bad Request', 'error');
          return;
        }
        
        if (result.success || response.ok) {
          showAlert(
            isEdit ? 'Ekspedisi berhasil diperbarui!' : 'Ekspedisi berhasil ditambahkan!', 
            'success'
          );
          
          clearDraft();
          
          setTimeout(() => {
            window.location.href = '?controller=Ekspedisi&action=index' + 
              (isEdit ? '&success=2' : '&success=1');
          }, 1500);
        } else {
          showAlert(result.error || result.message || 'Terjadi kesalahan', 'error');
        }
        
      } catch (error) {
        console.error('Network Error:', error);
        showAlert('Terjadi kesalahan jaringan: ' + error.message, 'error');
      } finally {
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        spinner.classList.add('d-none');
      }
    }

    async function loadAvailableCodes() {
      try {
        const response = await fetch('?api=ekspedisi&path=available-codes', {
          headers: getAuthHeaders()
        });
        
        const result = await response.json();
        
        if (result.success && result.data && result.data.data) {
          const codes = result.data.data;
          const kodeSelect = document.getElementById('kode');
          const currentValue = kodeSelect.value;
          
          kodeSelect.innerHTML = '<option value="">Pilih kode ekspedisi</option>';
          
          codes.forEach(code => {
            const option = document.createElement('option');
            option.value = code.kode;
            option.textContent = `${code.kode} - ${code.nama}`;
            if (currentValue === code.kode) {
              option.selected = true;
            }
            kodeSelect.appendChild(option);
          });
          
          const listContainer = document.getElementById('availableCodesList');
          if (listContainer) {
            listContainer.innerHTML = '';
            codes.slice(0, 5).forEach(code => {
              const div = document.createElement('div');
              div.className = 'mb-1';
              div.innerHTML = `
                <span class="badge badge-light">${code.kode}</span>
                ${code.nama}
              `;
              listContainer.appendChild(div);
            });
            
            if (codes.length > 5) {
              const small = document.createElement('small');
              small.className = 'text-muted';
              small.textContent = `dan ${codes.length - 5} lainnya...`;
              listContainer.appendChild(small);
            }
          }
          
          showAlert('Daftar kode ekspedisi berhasil diperbarui!', 'success');
        } else {
          showAlert('Gagal memuat kode ekspedisi', 'error');
        }
      } catch (error) {
        console.error('Error loading codes:', error);
        showAlert('Terjadi kesalahan saat memuat kode', 'error');
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
      
      const form = document.getElementById('ekspedisiForm');
      form.insertAdjacentHTML('afterbegin', alertHtml);
      
      setTimeout(() => {
        const alert = form.querySelector('.alert');
        if (alert) {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }
      }, 5000);
    }

    let autoSaveTimeout;
    function autoSaveDraft() {
      clearTimeout(autoSaveTimeout);
      autoSaveTimeout = setTimeout(() => {
        const formData = {
          kode: document.getElementById('kode').value,
          nama: document.getElementById('nama').value,
          status: document.getElementById('status').value
        };
        
        localStorage.setItem('ekspedisi_draft', JSON.stringify(formData));
        console.log('Draft saved');
      }, 2000);
    }

    function loadDraft() {
      const draft = localStorage.getItem('ekspedisi_draft');
      if (draft && !<?= json_encode($isEdit) ?>) {
        const data = JSON.parse(draft);
        if (confirm('Ditemukan draft yang belum disimpan. Muat draft?')) {
          document.getElementById('kode').value = data.kode || '';
          document.getElementById('nama').value = data.nama || '';
          document.getElementById('status').value = data.status || 'aktif';
        }
      }
    }

    function clearDraft() {
      localStorage.removeItem('ekspedisi_draft');
    }

    if (!<?= json_encode($isEdit) ?>) {
      document.getElementById('kode').addEventListener('change', autoSaveDraft);
      document.getElementById('nama').addEventListener('input', autoSaveDraft);
      document.getElementById('status').addEventListener('change', autoSaveDraft);
      
      loadDraft();
    }
  </script>

  <?php include 'template/script.php'; ?>
</body>

</html>