<?php
namespace AutomatedRoster\Controllers;

use TCPDF;

/**
 * Class for generating PDF file
 */
class CreatePDF extends TCPDF
{
  /**
   * Creata table data content file
   * 
   * @param array $header Array of table headers
   * @param array $data Array of table data content
   * @return void
   */
  public function table(array $header, array $data)
  {
    // Colors, Line width and Bold Font
    $this->SetFillColor(0, 102, 0);
    $this->SetTextColor(255);
    $this->SetDrawColor(102, 153, 153);
    $this->SetLineWidth(0.1);
    $this->SetFont('', 'B');

    // Header
    $w = array(25, 45, 90, 25, 25, 30, 20, 30);
    $num_headers = count($header);
    for ($i=0; $i < $num_headers; $i++) { 
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $this->Ln();

    // Color and font restoration
    $this->SetFillColor(224, 235, 255);
    $this->SetTextColor(0);
    $this->SetFont('');

    // Data
    $fill = 0;
    foreach ($data as $row) {
      $this->Cell($w[0], 6, $row['reg_no'], 'LR', 0, 'L', $fill);
      $this->Cell($w[1], 6, $row['fullname'], 'LR', 0, 'L', $fill);
      $this->Cell($w[2], 6, $row['duty'], 'LR', 0, 'L', $fill);
      $this->Cell($w[3], 6, $row['period_from'], 'LR', 0, 'L', $fill);
      $this->Cell($w[4], 6, $row['period_to'], 'LR', 0, 'L', $fill);
      $this->Cell($w[5], 6, $row['status'], 'LR', 0, 'L', $fill);
      $this->Cell($w[6], 6, $row['attendance_code'], 'LR', 0, 'L', $fill);
      $this->Cell($w[7], 6, $row['date_created'], 'LR', 0, 'L', $fill);
      $this->Ln();
      $fill = !$fill;
    }
    $this->Cell(array_sum($w), 0, '', 'T');
  }
}
