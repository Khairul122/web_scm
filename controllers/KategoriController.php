<?php
require_once 'models/KategoriModel.php';
require_once 'models/AuthModel.php';

class KategoriController {
    private $kategoriModel;
    private $authModel;

    public function __construct() {
        $this->kategoriModel = new KategoriModel();
        $this->authModel = new AuthModel();
    }

    public function index() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $kategoriData = $this->kategoriModel->getAllKategori();

        $data = [
            'user' => $user,
            'kategori' => $kategoriData['data']['data'] ?? [],
            'error' => $kategoriData['error'] ?? null
        ];

        include VIEWS_PATH . 'admin/kategori/index.php';
    }

    public function create() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $data = [
            'user' => $user,
            'action' => 'create',
            'kategori' => null
        ];

        include VIEWS_PATH . 'admin/kategori/form.php';
    }

    public function store() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $nama_kategori = $_POST['nama_kategori'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($nama_kategori)) {
            header('Location: /web_scm/admin/kategori/create?error=' . urlencode('Nama kategori wajib diisi'));
            exit;
        }

        $data = [
            'nama_kategori' => $nama_kategori,
            'deskripsi' => $deskripsi
        ];

        $result = $this->kategoriModel->createKategori($data);

        if ($result['success']) {
            header('Location: /web_scm/admin/kategori?success=' . urlencode('Kategori berhasil dibuat'));
        } else {
            header('Location: /web_scm/admin/kategori/create?error=' . urlencode($result['error'] ?? 'Gagal membuat kategori'));
        }
        exit;
    }

    public function edit($id) {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $kategoriData = $this->kategoriModel->getKategoriById($id);

        if (!$kategoriData['success']) {
            header('Location: /web_scm/admin/kategori?error=' . urlencode('Kategori tidak ditemukan'));
            exit;
        }

        $data = [
            'user' => $user,
            'action' => 'edit',
            'kategori' => $kategoriData['data']['data'] ?? null
        ];

        include VIEWS_PATH . 'admin/kategori/form.php';
    }

    public function update($id) {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $nama_kategori = $_POST['nama_kategori'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($nama_kategori)) {
            header('Location: /web_scm/admin/kategori/edit/' . $id . '?error=' . urlencode('Nama kategori wajib diisi'));
            exit;
        }

        $data = [
            'nama_kategori' => $nama_kategori,
            'deskripsi' => $deskripsi
        ];

        $result = $this->kategoriModel->updateKategori($id, $data);

        if ($result['success']) {
            header('Location: /web_scm/admin/kategori?success=' . urlencode('Kategori berhasil diperbarui'));
        } else {
            header('Location: /web_scm/admin/kategori/edit/' . $id . '?error=' . urlencode($result['error'] ?? 'Gagal memperbarui kategori'));
        }
        exit;
    }

    public function delete($id) {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $result = $this->kategoriModel->deleteKategori($id);

        if ($result['success']) {
            header('Location: /web_scm/admin/kategori?success=' . urlencode('Kategori berhasil dihapus'));
        } else {
            header('Location: /web_scm/admin/kategori?error=' . urlencode($result['error'] ?? 'Gagal menghapus kategori'));
        }
        exit;
    }

    public function products($id) {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $productsData = $this->kategoriModel->getKategoriProducts($id);

        $data = [
            'user' => $user,
            'kategori' => $productsData['data']['kategori'] ?? null,
            'products' => $productsData['data']['data'] ?? [],
            'error' => $productsData['error'] ?? null
        ];

        include VIEWS_PATH . 'admin/kategori/products.php';
    }
}