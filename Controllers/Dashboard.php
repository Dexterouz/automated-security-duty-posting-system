<?php
namespace AutomatedRoster\Controllers;

use AutomatedRoster\Config\Connection;
use Exception;

/**
 * Dashboard
 */
class Dashboard
{
  use Validation;

  protected $connection = null;

  public function __construct() {
    $this->connection = new Connection();
  }

  /**
   * Total number of staffs
   * @return int
   * @throws Exception
   **/
  public function totalStaff()
  {
    try {
      $queryStmt = "SELECT * FROM staffs";
      $execStmt = $this->connection->select($queryStmt);
      if ($execStmt->num_rows > 0) {
        return $execStmt->num_rows;
      }
    } catch (Exception $e) {
      print "An Error occurred! Message: {$e->getMessage()}";
    }
    return 0;
  }

  /**
   * Total staff present
   *
   * @return int
   * @throws Exception
   **/
  public function totalStaffPresent()
  {
    $status = 'present';

    try {
      $queryStmt = "SELECT * FROM rosters 
      WHERE status = ? AND date_created = DATE(CURDATE())";

      $execStmt = $this->connection->select($queryStmt, ['s', $status]);
      if ($execStmt->num_rows > 0) {
        return $execStmt->num_rows;
      }
    } catch (Exception $e) {
      print "An Error occurred! Message: {$e->getMessage()}";
    }
    return 0;
  }

  /**
   * Total staff absent
   *
   * @return int
   * @throws Exception
   **/
  public function totalStaffAbsent()
  {
    $status = 'absent';

    try {
      $queryStmt = "SELECT * FROM rosters 
      WHERE status = ? AND date_created = DATE(CURDATE())";

      $execStmt = $this->connection->select($queryStmt, ['s', $status]);

      if ($execStmt->num_rows > 0) {
        return $execStmt->num_rows;
      }
    } catch (Exception $e) {
      print "An Error occurred! Message: {$e->getMessage()}";
    }
    return 0;
  }

  /**
   * Fetch Data
   *
   * Fetch data for exporting
   *
   * @param string $month Month
   * @param string $year Year
   * @return array
   * @throws Exception
   **/
  public function fetchData(string $month, string $year)
  {
    $records = [];
    try {
      $queryStmt = "SELECT stf.reg_no, stf.fullname, dty.duty, 
      dty.period_from, dty.period_to, ros.status, ros.attendance_code,
      ros.date_created FROM staffs AS stf
      INNER JOIN rosters AS ros ON stf.staff_id = ros.staff_id 
      INNER JOIN duties AS dty ON ros.duty_id = dty.duty_id 
      WHERE MONTHNAME(ros.date_created) = ? 
      AND YEAR(ros.date_created) = ? ORDER BY ros.roster_id";

      $execStmt = $this->connection->select(
        $queryStmt, ['ss', $month, $year]
      );  

      if ($execStmt->num_rows > 0) {
        while ($row = $execStmt->fetch_assoc()) {
          $records[] = $row;
        }
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}"; 
    }

    return $records;
  }

  /**
   * Export as CSV
   *
   * Export monthly report sheet as .CSV File
   *
   * @param array $request Form Data request
   * @return bool
   * @throws Exception
   **/
  public function exportCSV(array $request)
  {
    extract($request);
    
    // Sanitize & validate input
    $month = $this->validate_text($month, 'month');
    $year = $this->validate_text($year, 'year');

    try {
      // if no error
      if ($this->flag) {
        // data 
        $records = $this->fetchData($month, $year);

        if (count($records) > 0) {
          // file name
          $file_name = "{$month}-{$year}-monthly-report-sheet.csv";

          // path save the file
          $directory = "..\\reports\\$file_name";

          // create a file handler
          $file_handler = fopen($directory, 'a+');

          // report header
          $header = [
            'REG NO', 'FULLNAME', 'DUTY', 
            'FROM', 'TO', 'ATTENDACE', 
            'ATTENDANCE CODE', 'DATE'
          ];

          // write file header
          fputcsv($file_handler, $header);

          // write file content
          foreach ($records as $record) {
            fputcsv($file_handler, $record);
          }

          // close file handler
          fclose($file_handler);

          // force download on export
          $file_basename = basename($directory);
          header("Content-Type: application/csv");
          header("Content-Disposition: attatchment; filename={$file_basename}");
          header("Content-Length: ".filesize($directory));

          readfile($directory);

          return true;
        } else {
          $this->error['export_err'] = "No report found for this month/year";
        }
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}";
    }

    return false;
  }

  /**
   * Export PDF
   *
   * Export monthly report sheet as .PDF file
   *
   * @param array $request Form data request
   * @return bool
   * @throws Exception
   **/
  public function exportPDF(array $request)
  {
    extract($request);
    $month = $this->validate_text($month, 'month');
    $year = $this->validate_text($year, 'year');

    // if no error
    if ($this->flag) {
      try {
        // file name
        $file_name = "{$month}-{$year}-monthly-report-sheet.pdf";

        // report header
        $headers = [
          'REG NO', 'FULLNAME', 'DUTY', 
          'FROM', 'TO', 'ATTENDACE', 
          'CODE', 'DATE'
        ];

        // data
        $data = $this->fetchData($month, $year);

        if (count($data) > 0) {
          $pdf = new CreatePDF("L");

          $pdf->SetCreator('Automatic Security Duty Posting System');
          $pdf->SetAuthor('UNN Security Department');
          $pdf->SetTitle($file_name);

          // set default header data
          $pdf->SetHeaderData('images/unn-logo-1.jpg', 53, 'Monthly Report Sheet', 'Report', array(255, 0, 0));

          // set header and footer fonts
          $pdf->SetHeaderFont(array('helvetica', '', 12));
          $pdf->SetFooterFont(array('helvetica', '', 12));

          // set margins
          $pdf->SetMargins(2, 2);
          $pdf->SetHeaderMargin(100);
          $pdf->SetFooterMargin();

          // set auto page breaks
          $pdf->SetAutoPageBreak(true);

          // set image scale factor
          $pdf->setImageScale(3.5);

          // set font
          $pdf->SetFont('helvetica', '', 10);

          // add a page
          $pdf->AddPage();

          // print colored table
          $pdf->table($headers, $data);

          // ---------------------------------------------------------

          // close and output PDF document
          $pdf->Output($file_name, 'I');
        } else {
          $this->error['export_err'] = "No report found for this month/year";
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
  }

  /**
   * Export main method
   *
   * @param array $request Form data request
   * @return void
   * @throws Exception
   **/
  public function export(array $request)
  {
    extract($request);

    switch ($doc_type) {
      case 'csv':
        $this->exportCSV($request);
        break;
      case 'pdf':
        $this->exportPDF($request);
        break;
      default:
        $this->error['export_err'] = "Invalid input!";
        break;
    }
  }
}
