<?php

function generateInvoiceNumber($id)
{
    return 'INV/' . date('d') . '/' . date('m') . '/' . date('Y') . '/' . str_pad($id, 4, '0', STR_PAD_LEFT);
}
