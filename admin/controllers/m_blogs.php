<?php

namespace admin\controllers;

use Exception;
use \resources\models\pagination as pagination;

class m_blogs extends controller {

 public $pagination = null, $sno = 0;

 public function __construct() {
  parent::__construct();
  $this->require_login('m-blogs');
  $this->list['categories'] = $this->get_blog_cats();
 }

 public function delete() {
  if (!$this->per_delete) {
   throw new Exception(_('You have no permission of delete.'));
  }
  $this->validate_delete_token(true);
  $this->db->delete('m_blogs', array('id' => $this->get('id')));
 }

 public function insert() {
  if (!$this->per_add) {
   throw new Exception(_('You have no permission of add.'));
  }
  $this->validate_post_token(true);
  $id = $this->db->insert('m_blogs', array(
   'category_id' => $this->post('category_id'),
   'show_homepage' => $this->post('show_homepage'),
   'blog_title' => $this->post('blog_title'),
   'blog_desc' => $this->post('blog_desc'),
   'blog_date' => $this->post('blog_date'),
   'blog_tags' => $this->post('blog_tags'),
   'page_title' => $this->post('page_title'),
   'page_heading' => $this->post('page_heading'),
   'page_url' => $this->post('page_url'),
   'meta_keywords' => $this->post('meta_keywords'),
   'meta_desc' => $this->post('meta_desc'),
   'publish' => $this->post('publish'),
   'add_date' => date('Y-m-d H:i:s')
  ));
  $this->save_file(array('blog_image'), 'm_blogs', $id);
 }

 public function update() {

  if (!$this->per_edit) {
   throw new Exception(_('You have no permission of update.'));
  }
  $this->validate_post_token(true);
  $id = $this->post('id');
  if ($id == '') {
   throw new Exception(_('Invalid ID for update!'));
  }
  $this->db->update('m_blogs', array(
   'category_id' => $this->post('category_id'),
   'show_homepage' => $this->post('show_homepage'),
   'blog_title' => $this->post('blog_title'),
   'blog_desc' => $this->post('blog_desc'),
   'blog_date' => $this->post('blog_date'),
   'blog_tags' => $this->post('blog_tags'),
   'page_title' => $this->post('page_title'),
   'page_heading' => $this->post('page_heading'),
   'page_url' => $this->post('page_url'),
   'meta_keywords' => $this->post('meta_keywords'),
   'meta_desc' => $this->post('meta_desc'),
   'publish' => $this->post('publish')), array(
   'id' => $id
  ));

  $this->save_file(array('blog_image'), 'm_blogs', $id);
 }

 public function select() {
  $query = "SELECT p.*, f.meta_value as blog_image FROM m_blogs p LEFT OUTER JOIN files f ON p.blog_image=f.id WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
  if (!$this->data = $this->db->select($query)) {
   $this->not_found();
  }
  $this->populate_post_data();
 }

 public function select_all() {
  global $dtoken;
  $dtoken = $this->delete_token();
  $where = "";
  if ($this->get('keyword') != '') {
   $where .= " WHERE blog_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
  }
  $query = "SELECT b.*, f.meta_value as blog_image FROM m_blogs b "
          . "LEFT OUTER JOIN files f ON b.blog_image=f.id "
          . "{$where} ORDER BY blog_title";
  $this->pagination = new pagination($this, $this->db, $query);
  $this->data = $this->pagination->paging('id');
  $this->sno = $this->pagination->get_sno();
 }
 
 function publish() {

   $this->db->update('m_blogs', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }


}
