<?php
/***********************************************************
 Copyright (C) 2011 Hewlett-Packard Development Company, L.P.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

/**
 * \file ajax_admin_scheduler 
 * \brief reaction when selecting one operation, different operation has different parameters to can be set
 **/

define("TITLE_ajax_admin_scheduler", _("URL"));

/**
 * \class ajax_admin_scheduler
 **/
class ajax_admin_scheduler extends FO_Plugin
{
  public $Name = "ajax_admin_scheduler";
  public $Title = TITLE_ajax_admin_scheduler;
  public $Version = "1.0";
  public $Dependency = array();
  public $DBaccess = PLUGIN_DB_UPLOAD;
  public $NoHTML     = 1; /* This plugin needs no HTML content help */
  public $LoginFlag = 0;

  /**
   * \brief get the job list for the specified operation
   * \param $type operation type, the job list is different
           according to the type of the operation
   * \return job list
   **/
  function JobListOption($type)
  {
    if (empty($type))
    {
      return '';
    }
    else if ('status' == $type || 'verbose' == $type)
    {
      $job_list_option .= "<option value='0'>scheduler</option>";
      $job_array = GetJobList("");
    }
    /* get job list from dB */
    if ('pause' == $type)  $job_array = GetJobList("Started");
    if ('restart' == $type)  $job_array = GetJobList("Paused");
    if ('priority' == $type || 'pause' == $type)  $job_array = GetJobList("");
    
    foreach ($job_array as $key => $value) 
    {
      $job_id = $value['jq_pk'];
      $job_list_option .= "<option value='$job_id'>$job_id</option>";
    } 
    
    return $job_list_option;
  }

  /**
   * \brief get the verbose list
   *        if the value of verbose is 1, set verbose as 1
   * \return verbose list
   **/
  function VerboseListOption()
  {
    $min = 1;
    $max = 3;
    for ($i = $min; $i <= $max; $i++)
    {
      $bitmask= pow(2, $i) - 1;
      $verbose_list_option .= "<option value='$bitmask'>$i</option>";
    }
    return $verbose_list_option;
  }

  /**
   * \brief get the priority list for setting, -20-20
   * \return priority list
   **/
  function PriorityListOption()
  {
    $min = -20;
    $max = 20;
    for ($i = $min; $i <= $max; $i++) 
    {
      if (0 == $i)
      {
        $priority_list_option .= "<option SELECTED value='$i'>$i</option>";
      } 
      else
      {
        $priority_list_option .= "<option value='$i'>$i</option>";
      }
    }
    return $priority_list_option ;
  }


  /**
   * \brief Generate the text for this plugin, when selecting one operation, return related html fragment to oprerate.
   */
  function Output()
  {
    if ($this->State != PLUGIN_STATE_READY) {
      return;
    }
    $V = "";
    switch ($this->OutputType)
    {
      case "XML":
        break;
      case "HTML":
       $operation = GetParm('operation', PARM_TEXT);
       $job_list_option = $this->JobListOption($operation);
       if ('pause' == $operation || 'restart' == $operation)
       {
         $text = _("Select a job");
         $V.= "$text: <select name='job_list' id='job_list'>$job_list_option</select>";
       }
       else if ('verbose'  == $operation) 
       {
         $verbose_list_option = $this->VerboseListOption();
         $text1 = _("Select the scheduler or a job");
         $text2 = _("Select a verbosity level");
         $V.= "$text1: <select name='job_list' id='job_list'>$job_list_option</select><br>$text2: <select name='level_list' id='level_list'>$verbose_list_option</select>";
       }
       else if ('status'  == $operation) 
       {
         $text = _("Select the scheduler or a job");
         $V.= "$text: <select name='job_list' id='job_list'>$job_list_option</select><br></select>";
       }
       else if ('priority'  == $operation)
       {
         $priority_list_option = $this->PriorityListOption();
         $text1 = _("Select a job");
         $text2 = _("Select a priority level");
         $V.= "$text1: <select name='job_list' id='job_list'>$job_list_option </select> <br>$text2: <select name='priority_list' id='priority_list'>$priority_list_option</select>";
       }
        break;
      case "Text":
        break;
      default:
        break;
    }
    if (!$this->OutputToStdout) {
      return ($V);
    }
    print ("$V");
    return;
  } // Output()
};
$NewPlugin = new ajax_admin_scheduler();
?>
