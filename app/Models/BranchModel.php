<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','address','city_code'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;



    public function getFilteredBranches($search = null, $date = null)
{
    $builder = $this->db->table('branches');

    if ($search) {
        $builder->like('name', $search);
        $builder->orLike('address', $search);
    }

    if ($date) {
        $builder->where('DATE(created_at)', $date); // jika ada field created_at
    }

    return $builder->get()->getResultArray();
}

}
