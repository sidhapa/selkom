<?php
namespace App\Models;

use CodeIgniter\Model;

class BranchOrderModel extends Model
{
    protected $table      = 'branch_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'request_code',
        'branch_id',
        'status',
        'note',
        'requested_at',
        'approved_at',
        'approved_by',
        'received_at',
        'received_by',
        'created_by',
        'created_at',
        'updated_at'
    ];

    public function getAll()
    {
        return $this->select('branch_orders.*, b.name AS branch_name')
            ->join('branches b', 'b.id = branch_orders.branch_id', 'left')
            ->orderBy('branch_orders.requested_at', 'DESC')
            ->findAll();
    }

    public function getByBranch($branchId)
    {
        return $this->select('branch_orders.*, b.name AS branch_name')
            ->join('branches b', 'b.id = branch_orders.branch_id', 'left')
            ->where('branch_orders.branch_id', $branchId)
            ->orderBy('branch_orders.requested_at', 'DESC')
            ->findAll();
    }

    public function getById($id)
    {
        return $this->select('branch_orders.*, b.name AS branch_name, u_req.name AS requester_name, u_app.name AS approver_name, u_rec.name AS receiver_name')
            ->join('branches b', 'b.id = branch_orders.branch_id', 'left')
            ->join('users u_req', 'u_req.id = branch_orders.created_by', 'left')
            ->join('users u_app', 'u_app.id = branch_orders.approved_by', 'left')
            ->join('users u_rec', 'u_rec.id = branch_orders.received_by', 'left')
            ->where('branch_orders.id', $id)
            ->first();
    }
}
