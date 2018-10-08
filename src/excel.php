<!DOCTYPE html>
<html lang="en">
  <head></head>
    <body>      
      <!-- PHP script starts here-->
       <?php 
        // Error logging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // include PHPexcel library
        include './Classes/PHPExcel.php';
        include './Classes/PHPExcel/IOFactory.php';
        require_once './Classes/PHPExcel.php';

        $recieved = json_decode($_POST['json'], true);
        $myFile = array_pop($recieved);
        $uniqNames = array();
        $length = count($recieved);    

        // Put all names into new array 
        foreach ($recieved as $value) {
                $uniqNames[] = $value[name];
        }
      
        $uniqNames = array_unique($uniqNames);
        sort($uniqNames);
        $uniqNames = array_values($uniqNames);
        $lengthUniqueNames = count($uniqNames);

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("Dubtools script");

        // Format spotting sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle("Stripped Script");

        $objPHPExcel->getActiveSheet()->getStyle("A1:A3000")->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle("B1:B3000")->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle("C1:C3000")->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle("D1:D3000")->getFont()->setSize(14);

        $objPHPExcel->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A1:Z1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF9fc9a0');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "ORG CHARACTER");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "ORG DIALOGUE");
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', "IS DUPLICATE");
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', "ORIGINAL LINE");

        $objPHPExcel->getActiveSheet()->getStyle('A1:A'.$objPHPExcel->getActiveSheet()->getHighestRow())
        ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getStyle('B1:B'.$objPHPExcel->getActiveSheet()->getHighestRow())
        ->getAlignment()->setWrapText(true); 

        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$objPHPExcel->getActiveSheet()->getHighestRow())
        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
      
        $objPHPExcel->getActiveSheet()->getStyle('D1:D'.$objPHPExcel->getActiveSheet()->getHighestRow())
        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

        

        // Write spotting data to XLSX file
        for ($i = 0; $i <= $length; $i++) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($i+2), $recieved[$i][name]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.($i+2), $recieved[$i][dialogue]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.($i+2), $recieved[$i][isDuplicate]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.($i+2), $recieved[$i][originalLine]);
        }

        // Write unique characters to new sheet 
        $objWorkSheet = $objPHPExcel->createSheet();  
        $objPHPExcel->setActiveSheetIndex(1);  
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "UNIQUE CHARACTERNAMES IN EPISODE");
        $objPHPExcel->getActiveSheet()->setTitle("Unique Characternames");
        $objPHPExcel->getActiveSheet()->getStyle("A1:A3000")->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);

        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFa7bee5');

        for ($i = 0; $i <= $lengthUniqueNames; $i++) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.($i+2), $uniqNames[$i]);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($myFile[filename].'.xlsx');
    ?>
</body>
</html>
