<?php
class Model_Report_Pointcard  {

	private $show;
	private $clazzes;
	private $teams;
	private $coaches;

	private function populate($showid,$typeid) {
		$m = new Model_DbTable_Classes();
		$rows = $m->fetchClasses($typeid);

		foreach ($rows as $row) {
			if ( $row->level == '20' ) {
				;
			} else if ( $row->level=='2A') {
				$this->clazzes[] = '2 - Walk, Trot, Canter';
				
			} else if ($row->level=='2B') {
				;
			} else if ( $row->level=='12A') {
				$this->clazzes[] = '12 - Intermediate';
				
			} else if ($row->level=='12B') {

				;			
			} else {
				$this->clazzes[] = $row->level . ' - ' . $row->name;
			}
		}

		$m = new Model_DbTable_Users();
		$s = new Model_DbTable_ShowTeams();
		$rows = $s->fetchInvitedTeamsEntered($showid);
		foreach ($rows as $row) {
			$this->teams[] = $row['TEAM_NAME'];
			
			$userid = $row['USER_ID'];
			$user = $m->fetchUser($userid);
			$this->coaches[] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
		}
	}

	public function createPointcard() {
		// set the show
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		$this->show = $ns->show;
		$this->populate($showid, $ns->show->typeid);

		$report = new Model_Report_Pointcarddoc();
		$cnt =0;
		foreach ($this->teams as $team) {
			$page = new Model_Report_Pointcardpage();
			$page->_showinfo = $this->show;
			$page->_clazzes = $this->clazzes;
			$page->_teaminfo = $team;
			$page->_coachinfo = current($this->coaches);
			
			next ($this->coaches);
			$page->_pagenum = ++$cnt;
			$report->addPage($page);
		}

		//$report->addPage($page);
		return $report;
	}



	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = 'ZZZ!!=' . $xtra . ' ,nnnnn ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
}