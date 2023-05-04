<?php

namespace admin\controllers;

use Exception;

class profile extends controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->page['name'] = _('Profile');
        $this->page['page_url'] = _('profile');
        $this->page['icon'] = 's7-user';
    }

    public function set_profile_image() {
        $uri = str_replace(' ', '+', $this->post('uri'));
        $image = explode('base64,', $uri);

        $filename = date('YmdHis') . '_' . rand(0000000, 9999999) . '.png';
        $path = $this->create_file_path(false, $filename);
        file_put_contents($path, base64_decode($image[1]));

        if ($this->user['image_id']) {
            $this->db_file_delete($this->user['image_id']);
        }

        $meta = array('folder' => $this->create_file_path(true), 'filename' => $filename, 'size' => filesize($path));
        $file_id = $this->db->insert('files', array(
            'type_id' => $this->user['id'],
            'table_name' => 'a_users',
            'type' => 'image',
            'name' => $this->post('filename'),
            'meta_value' => $this->json_encode($meta),
            'add_date' => date('Y-m-d H:i:s')
        ));
        $this->db->update('a_users', array(
            'image' => $file_id), array(
            'id' => $this->user['id']));
    }

    public function profile_update() {
        $this->validate_post_token(true);
        $id = $this->session('user', 'id');

        if ($this->db->value_exists('a_users', 'username', $this->replace_sql($this->post('username')), 'id', $id)) {
            throw new Exception(_('Username already exists.'));
        }
        $this->db->update('a_users', array(
            'first_name' => $this->post('first_name'),
            'last_name' => $this->post('last_name'),
            'display_name' => ($this->post('first_name') . ' ' . $this->post('last_name')),
            'username' => $this->post('username'),
            'mobile_no' => $this->post('mobile_no')), array(
            'id' => $id
        ));
    }

}
