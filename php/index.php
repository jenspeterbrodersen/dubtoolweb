<!DOCTYPE html>
<html lang="en">
    <head>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.15.0/lodash.min.js"></script>
      <link href="../css/main.css" rel="stylesheet">
      <script src="../js/pdf2xml.js"></script>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    </head>

    <body>
        <div id="wrapper" class="toggled">        
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1>Convert manuscript from XML to XLSX</h1>
                            <div class="container filebutton">
                                <label class="btn btn-default btn-file">Upload XML file...<input type="file" id="inputfile" style="display: none;"></label>
                                <label class="btn btn-default btn-file" id="download" style="display:none"><a href="file.csv" id="link">Download XLSX file</a></label>
                            </div>
                            <div class="container filename"><div id="list"></div></div>
                            <div class="container strip col-md-12"><div id="csv"></div></div>                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PHP script starts here-->
        <?php 
            include '../Classes/PHPExcel.php';
            include '../Classes/PHPExcel/IOFactory.php';
            require_once '../Classes/PHPExcel.php';

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
            $objWriter->save('../processed/'.$myFile[filename].'.xlsx');
        ?>
    </body>
</html>
