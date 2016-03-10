<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Progress extends CI_Controller {

	public function __construct()
        {
                parent::__construct();
                $this->load->model('staff_model');
                $this->load->model('projects_model');
                $this->load->model('vision_model');
                $this->load->model('task_model');
        }

	public function index()
	{
		redirect('Progress/index', 'refresh');
	}

	public function percentColour($percent){
		if ($percent>75){
			return 'danger';
		} elseif ($percent>50){
			return 'warning';
		} else {
			return 'success';
		}
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
			$employeeRates[$employeeBill['Employee']]=$tempRate2;
		}
		return $employeeRates;
	}

	public function view(){

		$task_table_flat=$this->task_model->get_tasks();
		foreach($task_table_flat as $tRow){
			$proj_names[$tRow['Project_Number']]=$tRow['Name'];
		}

		$employeeRates=$this->getEmployeeRates();

		//Get the summary of labour for pacHwy
		$visionData=$this->vision_model->get_LD_Summary($this->projects_model->get_projects());

		//Convert Hours to value
	    foreach ($visionData as $visionRow) {
			//Fill project name
			if (array_key_exists($visionRow['WBS1'], $proj_names)){ //Deal with case where project is not found
				$tallData[$visionRow['WBS1']]['name']=$proj_names[$visionRow['WBS1']];
			} else {
				$tallData[$visionRow['WBS1']]['name']=$visionRow['WBS1'];
			}

			$tallData[$visionRow['WBS1']][$visionRow['WBS2']]['name']=[$visionRow['WBS2']];

       		$tallData[$visionRow['WBS1']][$visionRow['WBS2']][$visionRow['Employee']]=$visionRow['Hours_Total']*$employeeRates[$visionRow['Employee']];
 		}

 		// Turn employee labour into sums for each task and project
		foreach($tallData as $tempProjNum => $tempProj){
			$projectTotal=0;
			foreach($tempProj as $tempTaskNum => $tempTask){
				if ($tempTaskNum!='name'){
					$taskTotal=0;
					foreach($tempTask as $tempEmployeeNum => $tempEmployee){
						if ($tempEmployeeNum!='name'){
							if ($tempEmployee>0){
								$taskTotal+=$tempEmployee;
							}
						}
					}
					unset($tallData[$tempProjNum][$tempTaskNum]); //Remove data which is not used later
					$projectTotal+=$taskTotal;
					$tallData[$tempProjNum]['children'][$tempTaskNum]['Actual']=$taskTotal;
					$tallData[$tempProjNum]['children'][$tempTaskNum]['Budget']=0;
					$tallData[$tempProjNum]['children'][$tempTaskNum]['children']=array();
				}
			}
			$tallData[$tempProjNum]['Total']=$projectTotal;
		}

		// get the budget for these tasks
		// dbg($tallData);
		$taskData=$this->task_model->get_tasks();
		// dbg($taskData);
		foreach($taskData as $taskBudget){
			//Fill project name
			if (array_key_exists($taskBudget['Project_Number'], $proj_names)){ //Deal with case where project is not found
				$tallData[$taskBudget['Project_Number']]['name']=$proj_names[$taskBudget['Project_Number']];
			} else {
				$tallData[$taskBudget['Project_Number']]['name']=$taskBudget['Project_Number'];
			}
			$tallData[$taskBudget['Project_Number']]['children'][$taskBudget['Task_Number']]['name']=$taskBudget['Task_Number'];

			if ($taskBudget['Budget_$']==''){ $taskBudget['Budget_$']=0; }
			$tallData[$taskBudget['Project_Number']]['children'][$taskBudget['Task_Number']]['Budget']=$taskBudget['Budget_$'];
			$tallData[$taskBudget['Project_Number']]['children'][$taskBudget['Task_Number']]['children']=array();

			if (!array_key_exists('Actual', $tallData[$taskBudget['Project_Number']]['children'][$taskBudget['Task_Number']])){
				$tallData[$taskBudget['Project_Number']]['children'][$taskBudget['Task_Number']]['Actual']=0;
			}

			if (!array_key_exists('Total', $tallData[$taskBudget['Project_Number']])){
				$tallData[$taskBudget['Project_Number']]['Total']=0;
			}
		}

		//Total the budgets for each project
		foreach($tallData as $tempProjNum => $tempProj){
			$projBudget=0;
			foreach($tempProj['children'] as $tempTaskNum => $tempTask){
				$projBudget+=$tempTask['Budget'];

				//Do percents for each task
				$tempReference=&$tallData[$tempProjNum]['children'][$tempTaskNum];
				// dbg($tempReference);
				if ($tempReference['Actual']==0){
					$tempReference['Percent']=0;
				} elseif ($tempReference['Budget']==0 or $tempReference['Actual']>$tempReference['Budget']) {
					$tempReference['Percent']=100;
				} else {
					$tempReference['Percent']=$tempReference['Actual']/$tempReference['Budget']*100;
				}
				$tempReference['Colour']=$this->percentColour($tempReference['Percent']);

			}
			$tallData[$tempProjNum]['Budget']=$projBudget;
			if ($tallData[$tempProjNum]['Total']==0){ //Value used is zero
				$tallData[$tempProjNum]['Percent']=0;
			} elseif ($projBudget==0 or $tallData[$tempProjNum]['Total']>$projBudget) { //over budget or no budget
				$tallData[$tempProjNum]['Percent']=100;
			} else {
				$tallData[$tempProjNum]['Percent']=$tallData[$tempProjNum]['Total']/$projBudget*100;
			}
			$tallData[$tempProjNum]['Colour']=$this->percentColour($tallData[$tempProjNum]['Percent']);
		}

		//Get the actual
		$visionData=$this->vision_model->get_LD_Summary($this->projects_model->get_projects());
		//Get the bugdeted
		$staffTaskHours=$this->task_model->get_staff_task_hours();

		//To lookup project Numbers
		$task_table_flat=$this->task_model->get_tasks();
		foreach($task_table_flat as $tRow){
			$proj_names[$tRow['Project_Number']]=$tRow['Name'];
		}

		//To lookup staff names
		$staff_table_flat=$this->staff_model->get_staff();
		foreach ($staff_table_flat as $tRow){
			$staff_names[$tRow['Employee']]=$tRow['FirstName'].' '.$tRow['LastName'];
		}

		//$tallData=array();


		//Adding the budgeted hours
		foreach($staffTaskHours as $budgetRow){
			//Fill project name
			$tallData[$budgetRow['Project_Number']]['name']=$proj_names[$budgetRow['Project_Number']];
			//Fill task name //Set task name to task number
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['name']=$budgetRow['Task_Number'];
			//Fill employee name
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['name']=$staff_names[$budgetRow['Employee']];

			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['Budgeted']=$budgetRow['Hours'];
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['Actual']=0;
			//Progress Bar is set to the default of Empty, green, 0%
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['Percent'] = 0;
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['PercentText'] = '0%';
			$tallData[$budgetRow['Project_Number']]['children'][$budgetRow['Task_Number']]['children'][$budgetRow['Employee']]['Colour'] = 'success';
		}

		// dbg($tallData);

		//Adding the actual hours
		foreach($visionData as $budgetRow){
			//Fill project name
			if (array_key_exists($budgetRow['WBS1'], $proj_names)){ //Deal with case where project is not found
				$tallData[$budgetRow['WBS1']]['name']=$proj_names[$budgetRow['WBS1']];
			} else {
				$tallData[$budgetRow['WBS1']]['name']=$budgetRow['WBS1'];
			}

			//Fill task name
			//Set task name to task number
			$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['name']=$budgetRow['WBS2'];
			

			//Fill employee name
			//check that employee exists (if not then put number instead)
			if (array_key_exists($budgetRow['Employee'], $staff_names)){
				$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]['name']=$staff_names[$budgetRow['Employee']];
			} else {
				$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]['name']=$budgetRow['Employee'];
			}


			if (!array_key_exists('Budgeted', $tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']])){
				$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]['Budgeted']=0;
			}

			//If Budgeted is 0 and Actual is 0 delete row
			if ($budgetRow['Hours_Total']==0 and $tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]['Budgeted']==0){
				unset($tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]); //Remove the employee/task/project entry
			} else {
				$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]['Actual']=$budgetRow['Hours_Total'];

				$progBarRef=&$tallData[$budgetRow['WBS1']]['children'][$budgetRow['WBS2']]['children'][$budgetRow['Employee']]; //Make code neater (I get sick of long variable names)
				
				
				if ($progBarRef['Actual']<=0){ //Case where actual hours are zero or negative
					$progBarRef['Percent']=0;
					$progBarRef['Colour']='success';
					$progBarRef['PercentText']=$progBarRef['Actual'].' hours worked of '.$progBarRef['Budgeted'];
				} else { //Positive hours actually worked
					if ($progBarRef['Actual']>$progBarRef['Budgeted']){
						//Over Budget
						$progBarRef['Percent']=100;
						$progBarRef['Colour']='danger';
						if ($progBarRef['Budgeted']==0){
							$progBarRef['PercentText']='No Hours Budgeted';
						} else {
							$progBarRef['PercentText']=number_format(($progBarRef['Actual']/$progBarRef['Budgeted']*100),2)."%";						}
				} else {
						//Under Budget
						$progBarRef['Colour']='info';
						$progBarRef['Percent']=number_format($progBarRef['Actual']/$progBarRef['Budgeted']*100,2);
						$progBarRef['PercentText']=$progBarRef['Percent'].'%';
					}
				}
			}
		}

		// dbg($tallData);

		$blah['data']['tallData']=$tallData;
		render('Progress/view', $blah);
	}
}
