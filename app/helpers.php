<?php
/*
 * 自定义函数库
 */

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/*
 * 栏目是否选择 active
 */
function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

/*
 * 剪切描述文字
 */
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return Str::limit($excerpt, $length);
}
