<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class m_blogs_comment extends controller {

  public $pagination = null, $sno = 0;
  public $replies = array();

  public function __construct() {
   parent::__construct();
   $this->require_login('m-blogs-comment');
  }

  public function delete() {

   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $id = $this->get('id');
   $this->db->delete('m_blogs_comment', array('id' => $id));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $parent = $this->get('id');
   $blog_id = $this->post('blog_id');
   $date = date('Y-m-d H:i:s');
   $id = $this->db->insert('m_blogs_comment', array(
       'parent_id' => $parent,
       'blog_id' => $blog_id,
       'user_id' => $this->session('user', 'id'),
       'name' => 'Admin',
       'comment' => $this->post('reply'),
       'verified' => 'Y',
       'publish' => 'Y',
       'type' => '1',
       'ip' => $this->server('REMOTE_ADDR'),
       'browser' => $this->get_browser(),
       'os' => $this->get_os(),
       'add_date' => $date
   ));
   $query = "UPDATE m_blogs SET total_comments=(SELECT COUNT(*) FROM m_blogs_comment WHERE blog_id='" . $blog_id . "' AND publish='Y' AND deleted='N' AND verified='Y') WHERE id='" . $blog_id . "'";
   $this->db->query($query);
  }

  public function select() {
   $query = "SELECT bc.*, b.blog_title, b.page_url "
           . "FROM m_blogs_comment bc "
           . "LEFT OUTER JOIN m_blogs b ON bc.blog_id=b.id "
           . "WHERE bc.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();

   $query = "SELECT * FROM m_blogs_comment WHERE parent_id='" . $this->data['id'] . "' AND deleted='N' ORDER BY add_date";
   $this->replies = $this->db->selectall($query);
  }

  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE parent_id=0";
   if ($this->get('keyword') != '') {
    $where .= " WHERE name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM m_blogs_comment {$where} ORDER BY id ASC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish() {

   $this->db->update('m_blogs_comment', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));

   $this->db->update('m_blogs_comment', array(
       'publish' => $this->post('publish')), array(
       'parent_id' => $this->post('id')
   ));
   $query = "SELECT * FROM m_blogs_comment WHERE id='" . $this->replace_sql($this->post('id')) . "'";
   if ($data = $this->db->select($query)) {
    $query = "UPDATE m_blogs SET total_comments=(SELECT COUNT(*) FROM m_blogs_comment WHERE blog_id='" . $data['blog_id'] . "' AND publish='Y' AND deleted='N' AND verified='Y') WHERE id='" . $data['blog_id'] . "'";
    $this->db->query($query);
   }
  }

 }
 