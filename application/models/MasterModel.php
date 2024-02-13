<?php

/**
 * MasterModel Class
 * @author  Rodilah Noer Rokhman <rodilahnoerrokhman @yahoo.co.id/@gmail.com>
 */
class MasterModel extends CI_Model
{
    function MasterModel()
    {
        parent::__construct();
        $this->db2 = $this->load->database('db2', TRUE);
    }

    public function count_data()
    {
        $query = $this->db2->query("SELECT Count(A.id) as count FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id")->row()->count;
        return $query;
    }
    public function count_data_miss()
    {
        $query = $this->db2->query("SELECT COUNT(id) as count 
        FROM master_song 
        WHERE id NOT IN (SELECT DISTINCT id_file FROM master_file) 
          AND id NOT IN (SELECT id_song FROM lost)")->row()->count;
        return $query;
    }
    public function count_data_duplicate()
    {
        $query = $this->db2->query("SELECT COUNT(*) as count
        FROM (
            SELECT id_file
            FROM master_file
            GROUP BY id_file
            HAVING COUNT(*) > 1
        ) AS duplicated_data;")->row()->count;
        return $query;
    }
    public function count_search_data($keyword)
    {
        $query = $this->db2->query("SELECT Count(A.id) as count FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id WHERE (A.id_file LIKE '%$keyword%' OR B.song LIKE '%$keyword%')")->row()->count;
        return $query;
    }

    public function get_data($limit, $offset)
    {
        $query = $this->db2->query("SELECT A.*,B.song FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id ORDER BY A.date_modified DESC LIMIT $limit OFFSET $offset");
        return $query->result_array();
    }

    public function get_data_miss($limit, $offset)
    {
        $query = $this->db2->query("SELECT id,song,format,csong 
        FROM master_song 
        WHERE id NOT IN (SELECT DISTINCT id_file FROM master_file) 
          AND id NOT IN (SELECT id_song FROM lost) ORDER BY id ASC LIMIT $limit OFFSET $offset");
        return $query->result_array();
    }
    public function get_data_duplicate($limit, $offset)
    {
        $query = $this->db2->query("SELECT mf.id_file,mf.extention,mf.id_file,mf.date_modified,mf.metadata,ms.song
        FROM master_file mf
        LEFT JOIN master_song ms ON mf.id_file = ms.id
        JOIN (
            SELECT id_file
            FROM master_file
            GROUP BY id_file
            HAVING COUNT(id_file) > 1
        ) dup ON mf.id_file = dup.id_file  ORDER BY id_file ASC LIMIT $limit OFFSET $offset");
        return $query->result_array();
    }

    public function count_search_results($keyword)
    {
        $query = $this->db2->query("SELECT Count(A.id) as count FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id WHERE (A.id_file LIKE '%$keyword%' OR B.song LIKE '%$keyword%')")->row()->count;
        return $query;
    }

    public function search_data_with_limit($keyword, $limit, $offset)
    {
        $query = $this->db2->query("SELECT A.*,B.song FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id WHERE (A.id_file LIKE '%$keyword%' OR B.song LIKE '%$keyword%') ORDER BY A.date_modified DESC LIMIT $limit OFFSET $offset");
        return $query->result_array();
    }
}
