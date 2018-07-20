<?php
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v)
            $d[$k] = utf8ize($v);
    } else if (is_string ($d))
        return utf8_encode($d);

    return $d;
}

function sqlReturn($data) {
    return print_r(json_encode(utf8ize($data)));
}
