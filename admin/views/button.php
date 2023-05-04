<?php

$str = '';
if ($type == 'YES_NO') {
    $cls = 'danger';
    if ($data['status'] == '') {
        $data['status'] = 'N';
    }
    if ($data['status'] == 'Y') {
        $cls = 'success';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'publish' => ($data['status'] == 'Y' ? 'N' : 'Y')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->yes_no[$data['status']] . '</button>';
} else if ($type == 'NUM_YES_NO') {
    $cls = 'success';
    if ($data['status'] == '') {
        $data['status'] = '0';
    }
    if ($data['status'] == '0') {
        $cls = 'danger';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'publish' => ($data['status'] == '0' ? '1' : '0')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->num_yes_no[$data['status']] . '</button>';
} else if ($type == 'FOR_ADMIN') {
    $cls = 'danger';
    if ($data['for_admin'] == '') {
        $data['for_admin'] = 'N';
    }
    if ($data['for_admin'] == 'Y') {
        $cls = 'success';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'for_admin' => ($data['for_admin'] == 'Y' ? 'N' : 'Y')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->yes_no[$data['for_admin']] . '</button>';
} else if ($type == 'FOR_USER') {
    $cls = 'danger';
    if ($data['for_user'] == '') {
        $data['for_user'] = 'N';
    }
    if ($data['for_user'] == 'Y') {
        $cls = 'success';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'for_user' => ($data['for_user'] == 'Y' ? 'N' : 'Y')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->yes_no[$data['for_user']] . '</button>';
} else if ($type == 'VERIFIED') {
    $cls = 'danger';
    if ($data['verified'] == '') {
        $data['verified'] = 'N';
    }
    if ($data['verified'] == 'Y') {
        $cls = 'success';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'verified' => ($data['verified'] == 'Y' ? 'N' : 'Y')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->yes_no[$data['verified']] . '</button>';
} else if ($type == 'COMMON') {
    $cls = 'danger';
    $field = $data['field'];
    if ($data[$field] == '') {
        $data[$field] = 'N';
    }
    if ($data[$field] == 'Y') {
        $cls = 'success';
    }

    $str .= '<button type="button" class="btn btn-' . $cls . ' btn-sm" data-ajaxify="true" data-url="' . (isset($data['url']) ? $data['url'] : $this->page['page_url']) . '" data-page="' . (isset($data['url']) ? false : true) . '" data-action="' . $data['action'] . '" data-app="' . ($this->encrypt_post_data(array('id' => $data['id'], 'field' => $field, $field => ($data[$field] == 'Y' ? 'N' : 'Y')))) . '" data-recid="' . ($data['action'] . '-' . $data['id']) . '">' . $this->yes_no[$data[$field]] . '</button>';
}
return $str;
 