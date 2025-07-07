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
                <h3 class="mb-0"><?php echo $data['action'] === 'create' ? 'Tambah Kategori' : 'Edit Kategori'; ?></h3>
                <a href="/web_scm/kategori" class="btn btn-secondary">
                  <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
              </div>

              <?php if (isset($data['error']) && !empty($data['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($data['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if (isset($data['success']) && !empty($data['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($data['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="card">
                <div class="card-body">
                  <form method="POST" action="<?php echo $data['action'] === 'create' ? '/web_scm/kategori/store' : '/web_scm/kategori/update'; ?>">
                    
                    <?php if ($data['action'] === 'edit' && $data['kategori']): ?>
                      <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['kategori']['id']); ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                      <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                      <input 
                        type="text" 
                        class="form-control" 
                        id="nama_kategori" 
                        name="nama_kategori" 
                        value="<?php echo $data['kategori'] ? htmlspecialchars($data['kategori']['nama_kategori']) : ''; ?>" 
                        required
                        placeholder="Masukkan nama kategori"
                      >
                    </div>
                    
                    <div class="mb-3">
                      <label for="deskripsi" class="form-label">Deskripsi</label>
                      <textarea 
                        class="form-control" 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="4"
                        placeholder="Masukkan deskripsi kategori (opsional)"
                      ><?php echo $data['kategori'] ? htmlspecialchars($data['kategori']['deskripsi']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i>
                        <?php echo $data['action'] === 'create' ? 'Simpan' : 'Update'; ?>
                      </button>
                      <a href="/web_scm/kategori" class="btn btn-light">
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