<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Report extends CI_Controller {
	public function __construct() {
		parent::__construct ();
			
		$this->load->library ( 'session' );
		$this->load->model ( 'report_model' );
		$this->load->model ( 'common_model' );
                $this->table = 'abc_student_rfid_mapping';
		
		//common_settings ();
	}
	public function index() {
		/*$condition = array(
				'abc_school_id'=>$this->session->userdata('school'),
	'abc_status'=>'Y');*/
		$this->load->view ( 'Report/report' );
	}
	public function absent() {
		$condition = array(
				'abc_school_id'=>$this->session->userdata('school'),
	      'abc_status'=>'Y');
		$data['grades']=$this->common_model-> get_all('abc_grade',$condition);
		$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
		$data['sths']=$this->report_model-> get_sth();
		$data['htss']=$this->report_model-> get_hts();
		$this->load->view ( 'Report/hash/absent/form',$data );
	}

	public function absent_report() {
		$data['a']=$this->input-> post('date');
		$data['grade']=$this->input-> post('grade');
		$data['division']=$this->input-> post('division');
		$data['sth']=$this->input-> post('sth');
		$data['hts']=$this->input-> post('hts');
		$school_name=$this->report_model->get_current_school_name();
		$table['header']="Absent Report on ".$data['a'].' of '.$school_name;
	    $table['abscents']=$this->report_model->get_absent($data);
		$this->load->view ( 'Report/hash/absent/report',$table );
	}
	public function absent_mail()
	{
			
	
		ob_start();
	
		$this->load->library("Pdf");
		$email=$this->input-> post('mail');
		$data['a']=$this->input-> post('date');
		$data['grade']=$this->input-> post('grade');
		$data['division']=$this->input-> post('division');
	    $table['abscents']=$this->report_model->get_absent($data);
	
		$pdf 								= new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->globalFooter					= "Report";
		$pdf->SetAuthor(SITE_NAME);
		$pdf->SetTitle('Report');
		$pdf->SetLineStyle(array('width' => 0.1, 'join' => 'miter', 'color' => array(215, 215, 215)));
		$pdf->SetSubject('Report Details');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 12, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(15);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('helvetica', '', 25, '', 'false');
		$pdf->SetFontSize(9);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage('P','A4','true','true');
		$pdf->Line(5,5,$pdf->getPageWidth()-10,5);
		$pdf->Line(5,5,5,$pdf->getPageHeight()-10);
		$pdf->Line($pdf->getPageWidth()-10,5,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
		$pdf->Line(5,$pdf->getPageHeight()-10,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
	   
	   
		$table['hide']=1;
		$html=$this->load->view ( 'Report/hash/absent/report',$table,true);
		$pdf->WriteHTML($html);
		ob_end_clean();
		$name='Absent_Report_'.date("d_m_y_h_i_s");
		$filename =DIR_ROOT.'/reports'.'//'.$name.'.pdf';
		
		$fileatt = $pdf->Output($filename, 'F');
		//$data = chunk_split( base64_encode(file_get_contents($filename)) );

        $fileatt =$this->sendEmail($email,'Absent Report','Report is attached.',$filename );
	
	
	
	
	
	}
	public function present_mail()
	{
			
	
		ob_start();
	
		$this->load->library("Pdf");
		$email=$this->input-> post('mail');
		 $data['a']=$this->input-> post('date');
		$data['grade']=$this->input-> post('grade');
		$data['division']=$this->input-> post('division');
	    $table['abscents']=$this->report_model->get_present($data);
	
		$pdf 								= new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->globalFooter					= "Report";
		$pdf->SetAuthor(SITE_NAME);
		$pdf->SetTitle('Report');
		$pdf->SetLineStyle(array('width' => 0.1, 'join' => 'miter', 'color' => array(215, 215, 215)));
		$pdf->SetSubject('Report Details');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 12, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(15);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('helvetica', '', 25, '', 'false');
		$pdf->SetFontSize(9);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage('P','A4','true','true');
		$pdf->Line(5,5,$pdf->getPageWidth()-10,5);
		$pdf->Line(5,5,5,$pdf->getPageHeight()-10);
		$pdf->Line($pdf->getPageWidth()-10,5,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
		$pdf->Line(5,$pdf->getPageHeight()-10,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
	   
	   
		$table['hide']=1;

		$html=$this->load->view ( 'Report/hash/present/report',$table,true);
		$pdf->WriteHTML($html);
		ob_end_clean();
		$name='Present_Report_'.date("d_m_y_h_i_s");
		$filename =DIR_ROOT.'/reports'.'//'.$name.'.pdf';
		
		$fileatt = $pdf->Output($filename, 'F');
		//$data = chunk_split( base64_encode(file_get_contents($filename)) );

        $fileatt =$this->sendEmail($email,'Present Report','Report is attached.',$filename );
	
	
	
	
	
	}
	public function sendEmail($email,$subject,$message,$filename)
    {
   
        $ci = get_instance();
        $ci->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "smartbusmonitor@gmail.com";
        $config['smtp_pass'] = "mondaylv";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        
        $ci->email->initialize($config);
        
        $ci->email->from('smartbusmonitor@gmail.com', 'Smartbus');
        $list = array('josephthomaa@gmail.com');
        $ci->email->to($email);
        //  $this->email->reply_to('my-email@gmail.com', 'Explendid Videos');
        $ci->email->subject($subject);
        $ci->email->message($message);
        $ci->email->attach($filename);
        // $ci->email->send();
        
        // $this->email->attach($filename);
        if($this->email->send())
        {
            echo '1';die;
        }
        else
        {
            print_r($this->email->print_debugger());die;
        }

    }
        public function present() {
                   $condition = array(
				'abc_school_id'=>$this->session->userdata('school'),
				'abc_status'=>'Y');
		
		$data['grades']=$this->common_model-> get_all('abc_grade',$condition);
		$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
		$data['sths']=$this->report_model-> get_sth();
		$data['htss']=$this->report_model-> get_hts();
		
		$this->load->view ( 'Report/hash/present/form',$data );
	}
		public function present_report() {
		$data['a']=$this->input-> post('date');
		$data['grade']=$this->input-> post('grade');
		$data['division']=$this->input-> post('division');
	    $table['abscents']=$this->report_model->get_present($data);
		$data['sth']=$this->input-> post('sth');
		$data['hts']=$this->input-> post('hts');
	    $school_name=$this->report_model->get_current_school_name();
	    $table['header']='Present Report on '.$data['a']. ' of '.$school_name;
		$this->load->view ( 'Report/hash/present/report',$table );
	}
	public function bus_daily_attendance() {
	    $condition = array(
		'abc_school_id'=>$this->session->userdata('school'),
	     'abc_status'=>'Y');
			$school_name=$this->report_model->get_current_school_name();
		$data['routes']=$this->common_model-> get_all('abc_route',$condition);
		//$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
		$data['header']='Bus Daily Attendance Of '.$school_name;
		$this->load->view ( 'Report/hash/bus_daily_attendance/form',$data );
	}
	public function student_bus_attendance() {
	    $condition = array(
				'abc_school_id'=>$this->session->userdata('school'),
				'abc_status'=>'Y');
		
		$data['students']=$this->common_model-> get_all('abc_student',$condition);
		//$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
		
		$this->load->view ( 'Report/hash/student_bus_attendance/form',$data );
	}
	public function school_bus_attendance() {
	$condition = array(
				'abc_school_id'=>$this->session->userdata('school'),
				'abc_status'=>'Y');
		
		$data['routes']=$this->common_model-> get_all('abc_route',$condition);
		$this->load->view ( 'Report/hash/smart_bus_attendance/form',$data );
	}
	public function bus_daily_attendance_report() {
		$data['a']=$this->input-> post('date');
		$data['route']=$this->input-> post('route');
		$school_name=$this->report_model->get_current_school_name();
		if($data['a']!='' && $data['route']!=''){
		
		$table['route_det']=$this->report_model->get_bus_daily_route_det($data);
                
		$table['total']=$this->report_model->get_bus_total_students($data);
    
                $table['pick_up']=$this->report_model->get_bus_picked_up($data);
     
		$table['dropped_off']=$this->report_model->get_bus_droped_off($data);
		$table['not_picked']=$table['total']-$table['pick_up'];
		$table['in_bus']=$table['pick_up']-$table['dropped_off'];
		$table['departure_time']=$this->report_model->get_bus_dep_time($data);
		$table['arrival_time']=$this->report_model->get_bus_arrival_time($data);
		$table['header']='Bus Daily Attendance Of '.$school_name;
		//print_r($table);die;
		$this->load->view ( 'Report/hash/bus_daily_attendance/report',$table );
	}else if($data['a'])
	{
		$condition = array(
		'abc_school_id'=>$this->session->userdata('school'),
	     'abc_status'=>'Y');
			//$school_name=$this->report_model->get_current_school_name();
		$data['routes']=$this->common_model-> get_all('abc_route',$condition);
		$data['date']=$data['a'];
		$data['header']='Bus Daily Attendance Of '.$school_name;
		//$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
		//$table['header']='Bus Daily Attendance Of '.$school_name;
		$this->load->view ( 'Report/hash/bus_daily_attendance/report_all',$data );
		
	}
    }
	public function bus_daily_attendance_report_mail()
	 {		
	
		ob_start();
	
		$this->load->library("Pdf");
		$email=$this->input-> post('mail');
		$data['a']=$this->input-> post('date');
		$data['route']=$this->input-> post('route');
		$school_name=$this->report_model->get_current_school_name();
		
		$pdf 								= new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->globalFooter					= "Report";
		//$pdf->SetAuthor(SITE_NAME);
		$pdf->SetTitle('Report');
		$pdf->SetLineStyle(array('width' => 0.1, 'join' => 'miter', 'color' => array(215, 215, 215)));
		$pdf->SetSubject('Report Details');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		//$pdf->setHeaderData('Header');
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 12, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(15);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('helvetica', '', 25, '', 'false');
		$pdf->SetFontSize(9);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage('P','A4','true','true');
		$pdf->Line(5,5,$pdf->getPageWidth()-10,5);
		$pdf->Line(5,5,5,$pdf->getPageHeight()-10);
		$pdf->Line($pdf->getPageWidth()-10,5,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
		$pdf->Line(5,$pdf->getPageHeight()-10,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
	   
	   
		$table['hide']=1;
		if($data['a']!=''&&$data['route']!=''){
		
			$table['route_det']=$this->report_model->get_bus_daily_route_det($data);
			$table['total']=$this->report_model->get_bus_total_students($data);

	        $table['pick_up']=$this->report_model->get_bus_picked_up($data);
			$table['dropped_off']=$this->report_model->get_bus_droped_off($data);
			$table['not_picked']=$table['total']-$table['pick_up'];
			$table['in_bus']=$table['pick_up']-$table['dropped_off'];
			$table['departure_time']=$this->report_model->get_bus_dep_time($data);
			$table['arrival_time']=$this->report_model->get_bus_arrival_time($data);
			$table['header']='Bus Daily Attendance Of '.$school_name;
			
         	// print_r($table);
			$html=$this->load->view ( 'Report/hash/bus_daily_attendance/report',$table, true );
		}else if($data['a'])
		{
			$condition = array(
			'abc_school_id'=>$this->session->userdata('school'),
		     'abc_status'=>'Y');
				//$school_name=$this->report_model->get_current_school_name();
			$table['routes']=$this->common_model-> get_all('abc_route',$condition);
			$table['date']=$data['a'];
			$table['header']='Bus Daily Attendance Of '.$school_name;
			//$data['divisions']=$this->common_model-> get_all('abc_division',$condition);
			//$table['header']='Bus Daily Attendance Of '.$school_name;
			$html=$this->load->view ( 'Report/hash/bus_daily_attendance/report_all',$table ,true);
		
		}
		//$html=$this->load->view ( 'Report/hash/bus_daily_attendance/report',$table,true );
		$pdf->WriteHTML($html);
		ob_end_clean();
		$name='bus_daily_attendance_report_'.date("d_m_y_h_i_s");
		$filename =DIR_ROOT.'/reports'.'//'.$name.'.pdf';
		
		$fileatt = $pdf->Output($filename, 'F');
		//$data = chunk_split( base64_encode(file_get_contents($filename)) );

        $fileatt =$this->sendEmail($email,'Bus Daily Attendance Report','Report is attached.',$filename );
	
	
	
	
	
	}
	public function student_bus_attendance_report() {
		$data['a']=$this->input-> post('date');
		$data['student']=$this->input-> post('student');
		$table['date']=$data['a'];
		$school_name=$this->report_model->get_current_school_name();
	    $table['student_det']=$this->report_model->get_student_det($data['student']);
	     //$sh_pick_up_stop=$table['student_det']->abc_sh_stop;
	    $hs=$this->report_model->get_student_hs($table['student_det'],$data['a']);
	    $table['hs_drop_off_det']=$hs['drop_off_det'];
        $table['hs_pick_up_det']=$hs['pick_up_det'];
        $sh=$this->report_model->get_student_sh($table['student_det'],$data['a']);
	    $table['sh_drop_off_det']=$sh['drop_off_det'];
	
        $table['sh_pick_up_det']=$sh['pick_up_det'];
      // print_r($table['sh_pick_up_det']);exit;
        $table['header']='Student Bus Attendance Of '.$school_name;
		$this->load->view ( 'Report/hash/student_bus_attendance/report',$table );
	}
	public function student_bus_attendance_report_mail()
	 {		
	
		ob_start();
	
		$this->load->library("Pdf");
		$email=$this->input-> post('mail');
		$data['a']=$this->input-> post('date');
		$data['student']=$this->input-> post('student');
		$table['date']=$data['a'];
		$school_name=$this->report_model->get_current_school_name();
	    $table['student_det']=$this->report_model->get_student_det($data['student']);
	     //$sh_pick_up_stop=$table['student_det']->abc_sh_stop;
	    $hs=$this->report_model->get_student_hs($table['student_det'],$data['a']);
	    $table['hs_drop_off_det']=$hs['drop_off_det'];
        $table['hs_pick_up_det']=$hs['pick_up_det'];
        $sh=$this->report_model->get_student_sh($table['student_det'],$data['a']);
	    $table['sh_drop_off_det']=$sh['drop_off_det'];
	
        $table['sh_pick_up_det']=$sh['pick_up_det'];
      // print_r($table['sh_pick_up_det']);exit;
        $table['header']='Student Bus Attendance Of '.$school_name;
	
		$pdf 								= new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->globalFooter					= "Report";
		//$pdf->SetAuthor(SITE_NAME);
		$pdf->SetTitle('Report');
		$pdf->SetLineStyle(array('width' => 0.1, 'join' => 'miter', 'color' => array(215, 215, 215)));
		$pdf->SetSubject('Report Details');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 12, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(15);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('helvetica', '', 25, '', 'false');
		$pdf->SetFontSize(9);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage('P','A4','true','true');
		$pdf->Line(5,5,$pdf->getPageWidth()-10,5);
		$pdf->Line(5,5,5,$pdf->getPageHeight()-10);
		$pdf->Line($pdf->getPageWidth()-10,5,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
		$pdf->Line(5,$pdf->getPageHeight()-10,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
	   
	   
		$table['hide']=1;

		$html=$this->load->view ( 'Report/hash/student_bus_attendance/report',$table,true );
		$pdf->WriteHTML($html);
		ob_end_clean();
		$name='studentBusAttendance_report_'.date("d_m_y_h_i_s");
		$filename =DIR_ROOT.'/reports'.'//'.$name.'.pdf';
		
		$fileatt = $pdf->Output($filename, 'F');
		//$data = chunk_split( base64_encode(file_get_contents($filename)) );

        $fileatt =$this->sendEmail($email,'Student Bus Attendance Report','Report is attached.',$filename );

	}
        
	public function school_bus_attendance_report() {
		$data['start_date']=$this->input-> post('start_date');
		$data['end_date']=$this->input-> post('end_date');
		$data['route']=$this->input-> post('route');
                
                $school_name=$this->report_model->get_current_school_name();
                $vehicle=$this->report_model->get_bus_from_route($data['route']);
                
                $registration_number=$vehicle->reg_no;
                
                $table['stops']=$this->report_model->get_stops($data['route']);
                
                
                $stops=array();
                
                foreach ($table['stops'] as $key => $value) {
                    array_push($stops, $value['stop_name']);
                }
               
                
                
             $table['rfid_table_from_stops']=$this->report_model->rfid_table_from_stop( $stops,$data['start_date'],$data['end_date']);
             $table['header']='School Bus Attendance Report from '.$data['start_date'].' to '.$data['end_date'] .' Of '.$school_name;
	    //$last_stop=$this->report_model->get_last_stop_from_routeID($data['route']);
	     $table['last_stop']=$last_stop->stop_name;
	     //$route=$this->db->where ( 'abc_route_id',$data['route'])->get ( 'abc_route' )->row ();
             //print_r($route);die;
             //$table['routename']=$route->abc_route_name;
        	$this->load->view ( 'Report/hash/smart_bus_attendance/report',$table );
	}
        
        public function student_rfid_mapping()
        {
            $rfId = $_SERVER['HTTP_RFID'];
            $busId = $_SERVER['HTTP_DEVICEID'];
            $dateTime = $_SERVER['HTTP_DATETIME'];
            $stopName = $_SERVER['HTTP_STOPNAME'];
            
            $result['status'] = false;
            $result['message'] = 'Failed to update data!';
            
            $studentDetails = $this->report_model->get_student_from_rfid($rfId);
            $studentRfidCount = $this->report_model->get_student_rfid_entries($rfId,$dateTime);
            $studentRfidData = end($studentRfidCount);
            $dropFlag = $studentRfidData->abc_st_rfid_dropped_flag;
            $stRfid = $studentRfidData->id;
            
            $data = array(
                'abc_st_rfid'=>$rfId,
                'abc_st_rfid_date'=>$dateTime,
                'abc_st_rfid_name'=>$studentDetails[0]->abc_name,
                'abc_st_rfid_grade'=>$studentDetails[0]->abc_grade_id,
                'abc_st_rfid_section'=>$studentDetails[0]->abc_division_id,
                'abc_st_rfid_bus_id'=>$busId,
                'abc_st_rfid_picked_time'=>$dateTime,
                'abc_st_rfid_picked_stop'=>$stopName,
            );
          
            $updateData = array(
                'abc_st_rfid_dropped_time'=>$dateTime,
                'abc_st_rfid_dropped_stop'=>$stopName,
                'abc_st_rfid_dropped_flag'=>1    
            );
            
            $condition = array (
                    'id' => $stRfid
            );
            
            if(count($studentRfidCount)==0){
                
                //insert
                $insert = $this->common_model->insert_data('abc_student_rfid_mapping',$data);
                $result['status'] = true;
                $result['message'] = 'Data inserted successfully!';
            }
             else if(count($studentRfidCount)==1){
                if($dropFlag==0){
                    //update
                    $update = $this->common_model->update_row($updateData,$condition,'abc_student_rfid_mapping');
                     $result['status'] = true;
                     $result['message'] = 'Data updated successfully!';
                    
                }else{
                    //insert
                    $insert = $this->common_model->insert_data('abc_student_rfid_mapping',$data);
                     $result['status'] = true;
                     $result['message'] = 'Data inserted successfully!';
                }
            }else if(count($studentRfidCount)==2){
                if($dropFlag==0){         
                    //update
                    $update = $this->common_model->update_row($updateData,$condition,'abc_student_rfid_mapping');
                     $result['status'] = true;
                     $result['message'] = 'Data Updated successfully!';
                    
                }else{
                     $result['status'] = false;
                     $result['message'] = 'Failed to update data!';
                }
            }else{
                 $result['status'] = false;
                 $result['message'] = 'Failed to update data!';
            }
            print_r(json_encode($result));exit;
        }
        
        
       public function school_bus_attendance_report_mail()
	 {		
	
		ob_start();
	
		$this->load->library("Pdf");
		$email=$this->input-> post('mail');
		$data['start_date']=$this->input-> post('start_date');
		$data['end_date']=$this->input-> post('end_date');
		$data['route']=$this->input-> post('route');
        $school_name=$this->report_model->get_current_school_name();
        $vehicle=$this->report_model->get_bus_from_route($data['route']);
        $registration_number=$vehicle->reg_no;

	    $table['stops']=$this->report_model->get_stops($data['route']);
	    $stops=array();
	    foreach ($table['stops'] as $key => $value) {
	    	array_push($stops, $value['stop_name']);
	    }
	    $table['rfid_table_from_stops']=$this->report_model->rfid_table_from_stops( $stops,$data['start_date'],$data['end_date'],$registration_number);
	     $table['header']='Smart Bus Attendance Report from '.$data['start_date'].' to '.$data['end_date'] .' Of '.$school_name;
	     $last_stop=$this->report_model->get_last_stop_from_routeID($data['route']);
	     $table['last_stop']=$last_stop->stop_name;
	     $route=$this->db->where ( 'abc_route_id',$data['route'])->get ( 'abc_route' )->row ();
	     $table['routename']=$route->abc_route_name;

		$pdf 								= new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->globalFooter					= "Report";
		//$pdf->SetAuthor(SITE_NAME);
		$pdf->SetTitle('Report');
		$pdf->SetLineStyle(array('width' => 0.1, 'join' => 'miter', 'color' => array(215, 215, 215)));
		$pdf->SetSubject('Report Details');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 12, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(15);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('helvetica', '', 25, '', 'false');
		$pdf->SetFontSize(9);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->AddPage('P','A4','true','true');
		$pdf->Line(5,5,$pdf->getPageWidth()-10,5);
		$pdf->Line(5,5,5,$pdf->getPageHeight()-10);
		$pdf->Line($pdf->getPageWidth()-10,5,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
		$pdf->Line(5,$pdf->getPageHeight()-10,$pdf->getPageWidth()-10,$pdf->getPageHeight()-10);
	   
	   
		$table['hide']=1;

		$html=$this->load->view ( 'Report/hash/smart_bus_attendance/report',$table,true );
		$pdf->WriteHTML($html);
		ob_end_clean();
		$name='SmarttBusAttendance_report_'.date("d_m_y_h_i_s");
		$filename =DIR_ROOT.'/reports'.'//'.$name.'.pdf';
		
		$fileatt = $pdf->Output($filename, 'F');
		//$data = chunk_split( base64_encode(file_get_contents($filename)) );

            $fileatt =$this->sendEmail($email,'Smart Bus Attendance Report','Report is attached.',$filename );
	
	}

	
}
