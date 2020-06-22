<?php
/*
 * 自定义函数库
 */

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
