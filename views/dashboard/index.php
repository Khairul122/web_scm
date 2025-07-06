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
            <div class="col-sm-12 mb-4">
              <h4>Selamat datang, <strong><?= htmlspecialchars($user['username']) ?></strong></h4>
              <p>Berikut ini adalah ringkasan data klasifikasi sawit Anda:</p>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
              <div class="card text-white bg-success">
                <div class="card-body">
                  <h4 class="card-title">Buah Sawit Matang</h4>
                  <h2 class="mt-3"><?= $statistik['buah_matang'] ?></h2>
                </div>
              </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
              <div class="card text-white bg-danger">
                <div class="card-body">
                  <h4 class="card-title">Buah Sawit Tidak Matang</h4>
                  <h2 class="mt-3"><?= $statistik['buah_tidak_matang'] ?></h2>
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
