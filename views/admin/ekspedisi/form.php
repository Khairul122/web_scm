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
                <h3 class="mb-0"><?php echo $data['action'] === 'create' ? 'Tambah Kurir' : 'Edit Kurir'; ?></h3>
                <a href="/web_scm/ekspedisi" class="btn btn-secondary">
                  <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
              </div>

              <?php if (isset($data['error']) && !empty($data['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($data['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="card">
                <div class="card-body">
                  <form method="POST" action="<?php echo $data['action'] === 'create' ? '/web_scm/ekspedisi/store' : '/web_scm/ekspedisi/update'; ?>">
                    
                    <?php if ($data['action'] === 'edit' && $data['kurir']): ?>
                      <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['kurir']['id']); ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                      <label for="kode" class="form-label">Kode Kurir <span class="text-danger">*</span></label>
                      <?php if ($data['action'] === 'create'): ?>
                        <select class="form-select" id="kode" name="kode" required>
                          <option value="">Pilih Kode Kurir</option>
                          <?php if (!empty($data['available_codes'])): ?>
                            <?php foreach ($data['available_codes'] as $code): ?>
                              <option value="<?php echo htmlspecialchars($code['kode']); ?>">
                                <?php echo htmlspecialchars($code['kode']); ?> - <?php echo htmlspecialchars($code['nama']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      <?php else: ?>
                        <input 
                          type="text" 
                          class="form-control" 
                          id="kode" 
                          name="kode" 
                          value="<?php echo $data['kurir'] ? htmlspecialchars($data['kurir']['kode']) : ''; ?>" 
                          required
                          readonly
                        >
                      <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                      <label for="nama" class="form-label">Nama Kurir <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control" 
                        id="nama" 
                        name="nama" 
                        value="<?php echo $data['kurir'] ? htmlspecialchars($data['kurir']['nama']) : ''; ?>" 
                        required
                        placeholder="Masukkan nama kurir"
                      >
                    </div>
                    
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i>
                        <?php echo $data['action'] === 'create' ? 'Simpan' : 'Update'; ?>
                      </button>
                      <a href="/web_scm/ekspedisi" class="btn btn-light">
                        <i class="mdi mdi-cancel"></i> Batal
                      </a>
                    </div>
                    
                  </form>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'template/script.php'; ?>
</body>

</html>