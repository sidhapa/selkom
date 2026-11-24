<?php

namespace App\Controllers;

use App\Models\StockCardModel;

class StockCard extends BaseController
{
    protected $stockCardModel;

    public function __construct()
    {
        $this->stockCardModel = new StockCardModel();
    }

    public function index()
    {
        $data['stock_cards'] = $this->stockCardModel
            ->select('stock_cards.*, products.name as product_name, branches.name as branch_name')
            ->join('products', 'products.id = stock_cards.product_id')
            ->join('branches', 'branches.id = stock_cards.branch_id')
            ->findAll();

        return view('stock_card/index', $data);
    }
}
