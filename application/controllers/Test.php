<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
    }
    public function index()
    {
        $this->db->from('ems_sales_po_details p');
        $this->db->select('p.variety_id,SUM(p.quantity) total_po');
        $this->db->select('SUM(r.quantity_return) total_return,r.revision');
        $this->db->join('ems_sales_po_returns r','r.sales_po_detail_id = p.id','INNER');
        $this->db->where('p.variety_id',3);
        //$this->db->where('p.quantity <=r.quantity_return');
        //$results=$this->db->get()->result_array();
        $results=$this->db->get()->row_array();
        echo '<pre>';
        //print_r($results);
        print_r(($results['total_po']-$results['total_return'])/100);
        echo '</pre>';
    }

}
