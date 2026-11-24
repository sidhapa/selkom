<?php

namespace App\Models;

use CodeIgniter\Model;

class AlokasiModel extends Model
{
    protected $table = 'alokasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['produk_id', 'ke_user_id', 'jumlah', 'keterangan', 'status'];
    protected $useTimestamps = true;

    public function getWithProdukUser()
    {
        return $this->select('alokasi.*, produk.nama_produk, users.nama AS nama_cabang')
                    ->join('produk', 'produk.id = alokasi.produk_id')
                    ->join('users', 'users.id = alokasi.ke_user_id');
    }
}
