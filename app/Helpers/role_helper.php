<?php

function user_role()
{
    return session()->get('role');
}

function user_id()
{
    return session()->get('user_id');
}

function is_pusat()
{
    return session()->get('role') === 'pusat';
}

function is_cabang()
{
    return session()->get('role') === 'cabang';
}
