<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timesheets extends CI_Controller {

	public function __construct()
        {
                parent::__construct();
                $this->load->model('staff_model');
                $this->load->model('projects_model');
                $this->load->model('vision_model');
        }

	public function index()
	{

		redirect('Timesheets/index', 'refresh');
	}
	
	//Andrews function
	public function bill() //Makes the bill button on the left of the screen redirect to the view screen
	{
		redirect('Timesheets/view', 'refresh'); //Whoo it works...
	}

	public function request()
	{

		$start = $this->input->post('startDate');
		$startdate = str_replace('/', '-', $start);
		$finish = $this->input->post('finishDate');
		$finishdate = str_replace('/', '-', $finish);
		redirect('Timesheets/'.$this->input->post('format').'/'.date('Y-m-d', strtotime($startdate))."/".date('Y-m-d', strtotime($finishdate)));
	}

	public function download_request()
	{

		$start = $this->input->post('startDate');
		$startdate = str_replace('/', '-', $start);
		$finish = $this->input->post('finishDate');
		$finishdate = str_replace('/', '-', $finish);
		redirect('Timesheets/download/'.date('Y-m-d', strtotime($startdate))."/".date('Y-m-d', strtotime($finishdate)));
	}

	public function getEmployeeRates(){
		// Create an array which converts from employees to rates
		$rateQueryResult=$this->staff_model->get_rates();
		foreach($rateQueryResult as $tempRate){
			$rateLookups[$tempRate['Level']]=$tempRate['Rate'];
		}
		//Get all the peoples levels
		$employeeBillLevels=$this->vision_model->get_people_levels();

		//Change all the levels into rates
		foreach ($employeeBillLevels as $employeeBill){
			if (array_key_exists($employeeBill['BillingCategory'], $rateLookups)){
				$tempRate2=$rateLookups[$employeeBill['BillingCategory']];
			} else {
				$tempRate2=0;
			}
			$employeeRates[$employeeBill['Employee']]['Rate']=$tempRate2;
			$employeeRates[$employeeBill['Employee']]['Level']=$employeeBill['BillingCategory'];
			$employeeRates[$employeeBill['Employee']]['FirstName']=$employeeBill['FirstName'];
			$employeeRates[$employeeBill['Employee']]['LastName']=$employeeBill['LastName'];
		}

		return $employeeRates;
	}

	public function view($startDate = NULL, $finishDate = NULL)
	{
		

		if($startDate && $finishDate){
			$output['data']['visionRateLookup']=$this->getEmployeeRates();
			$output['data']['startDate'] = $startDate;
			$output['data']['finishDate'] = $finishDate;
			$output['data']['Staff'] = $this->staff_model->get_staff();
			$output['data']['Projects'] = $this->projects_model->get_projects();
			
			// $output['data']['LD'] = $this->vision_model->get_LD('2016-01-17', '2016-01-23', $output['data']['Projects']);
			$output['data']['LD'] = $this->vision_model->get_LD($startDate, $finishDate, $output['data']['Projects']);

			//Flatten Staff & Projects
			foreach($output['data']['Staff'] as $record){
				$output['data']['table']['Staff'][$record['Employee']] = $record;
			}
			foreach($output['data']['Projects'] as $record){
				$output['data']['table']['Projects'][$record['Project_Number']] = $record;
			}
			

			//Do manual joins for form a single table
			
			foreach($output['data']['LD'] as $record){
				$record['Project_Record'] = $output['data']['table']['Projects'][$record['WBS1']];
				// dbg( $output['data']['table']['Staff']);
				if (array_key_exists($record['Employee'], $output['data']['table']['Staff'])){
					$record['Staff_Record'] = $output['data']['table']['Staff'][$record['Employee']];
					$record['Staff_Rate'] =$output['data']['table']['Staff'][$record['Employee']]['Rate'];
					$record['Staff_Level'] =$output['data']['table']['Staff'][$record['Employee']]['Role_ID'];
				} else {
					$record['Staff_Record']['FirstName'] = $output['data']['visionRateLookup'][$record['Employee']]['FirstName'];
					$record['Staff_Record']['LastName'] = $output['data']['visionRateLookup'][$record['Employee']]['LastName'];;
					$record['Staff_Record']['Role'] = '';
					$record['Staff_Record']['Internal_Only'] = 0;
					$record['Staff_Rate'] = $output['data']['visionRateLookup'][$record['Employee']]['Rate'];
					$record['Staff_Level'] = $output['data']['visionRateLookup'][$record['Employee']]['Level'];
				}

				if(!$record['Staff_Record']['Internal_Only'] && !$record['Project_Record']['Invalid']){
					$output['data']['table']['LD'][$record['PKey']] = $record;		
				}
				
			}

			render('Timesheets/view', $output);
		} else {
			render('Timesheets/view_input_required');
		}
	}

	public function download($startDate, $finishDate){

		$output['data']['Staff'] = $this->staff_model->get_staff();
		$output['data']['Projects'] = $this->projects_model->get_projects();
			$output['data']['visionRateLookup']=$this->getEmployeeRates();
		
		// $output['data']['LD'] = $this->vision_model->get_LD('2016-01-17', '2016-01-23', $output['data']['Projects']);
		$output['data']['LD'] = $this->vision_model->get_LD($startDate, $finishDate, $output['data']['Projects']);

		//Flatten Staff & Projects
		foreach($output['data']['Staff'] as $record){
			$output['data']['table']['Staff'][$record['Employee']] = $record;
		}
		foreach($output['data']['Projects'] as $record){
			$output['data']['table']['Projects'][$record['Project_Number']] = $record;
		}
		

		//Do manual joins for form a single table
		
			foreach($output['data']['LD'] as $record){
				$record['Project_Record'] = $output['data']['table']['Projects'][$record['WBS1']];
				// dbg( $output['data']['table']['Staff']);
				if (array_key_exists($record['Employee'], $output['data']['table']['Staff'])){
					$record['Staff_Record'] = $output['data']['table']['Staff'][$record['Employee']];
					$record['Staff_Rate'] =$output['data']['table']['Staff'][$record['Employee']]['Rate'];
					$record['Staff_Level'] ='L'.$output['data']['table']['Staff'][$record['Employee']]['Role_ID'];
				} else {
					$record['Staff_Record']['FirstName'] = $output['data']['visionRateLookup'][$record['Employee']]['FirstName'];
					$record['Staff_Record']['LastName'] = $output['data']['visionRateLookup'][$record['Employee']]['LastName'];;
					$record['Staff_Record']['Role'] = '';
					$record['Staff_Record']['Internal_Only'] = 0;
					$record['Staff_Rate'] = $output['data']['visionRateLookup'][$record['Employee']]['Rate'];
					$record['Staff_Level'] = 'L'.$output['data']['visionRateLookup'][$record['Employee']]['Level'];
				}

				if(!$record['Staff_Record']['Internal_Only'] && !$record['Project_Record']['Invalid']){
					$output['data']['table']['LD'][$record['PKey']] = $record;		
				}
				
			}


		$this->load->library('excel');
		$fileType = 'Excel2007';
		$fileName = APPPATH.'../standards/Template.xlsx';


		//dbg($fileName);

		// Read the file
		$objReader = PHPExcel_IOFactory::createReader($fileType);
		$objPHPExcel = $objReader->load($fileName);


		// Change the headings file
		$objPHPExcel->setActiveSheetIndex(0)
		            // ->setCellValue('A4', 'Hello')
		            ->setCellValue('C6', 'SMEC / OPUS')
		            ->setCellValue('C7', 'SMC')
		            ->setCellValue('M5', strftime('%e/%m/%G', strtotime($startDate)))
		            ->setCellValue('M7', strftime('%e/%m/%G', strtotime($finishDate)));
		            
		//Loop through all rows
		$counter = 1;
		foreach($output['data']['table']['LD'] as $record){
			$rowNum = 11+$counter;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A$rowNum", $counter)
						->setCellValue("B$rowNum", $record['Project_Record']['Design_Lot_ID'])
						->setCellValue("C$rowNum", $record['Project_Record']['Description'])
						->setCellValue("D$rowNum", 'Opus')
						->setCellValue("E$rowNum", $record['Staff_Record']['FirstName'])
						->setCellValue("F$rowNum", $record['Staff_Record']['LastName'])
						->setCellValue("G$rowNum", $record['Staff_Level'])
						->setCellValue("H$rowNum", $record['Staff_Record']['Role'])
						->setCellValue("I$rowNum", $record['RegHrs'])
						->setCellValue("K$rowNum", $record['Staff_Rate'])
						->setCellValue("L$rowNum", round($record['Staff_Rate']*$record['RegHrs'], 2));
			$counter++;
		}

		$filename = "W2B-PC0-0-ZE-TMP-0001-B Opus Timesheet-".str_replace("-","_",$startDate)."-".str_replace("-","_",$finishDate).".xlsx";
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		// Write the file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $fileType);
		$objWriter->save('php://output');
	}
}
