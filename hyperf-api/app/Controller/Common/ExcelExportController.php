<?php

namespace App\Controller\Common;

use App\Middleware\RequestMiddleware;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;


/**
 * Excel export controller
 * @Controller(prefix="excel")
 */
class ExcelExportController extends AbstractController
{
    /**
     * Get Excel content data
     * @RequestMapping(path="excel_content", methods="post")
     * @Middleware(RequestMiddleware::class)
     */
    public function getExcelContent()
    {
        $requestParams = $this->request->all();
        $data = $requestParams['data'] ?? []; //Data module
        $percentArr = $requestParams['percent_arr'] ?? []; //Whether a percentage field
        $tableHeader =  $requestParams['table_header'] ?? []; //Head field
        $tableHeaderMean =  $requestParams['table_header_mean'] ?? []; //Head field explanation
        $stringArr = $requestParams['string_arr'] ?? []; // The fields that need to be exported in the form of characters (prevent scientific counting method)
        $time =  $requestParams['time'] ?? 'time'; //Time field

        $tableContent = '';
        $tableContent .= '<table id="tables"><tbody><tr>';
        foreach ($tableHeader as $k => $v) {
            $tableContent .= '<th>' . $tableHeaderMean[$v] . '</th>';
        }
        $tableContent .= '</tr>';
        foreach($data as $k => $v) {
            $tableContent .= '<tr>';
            foreach ($tableHeader as $k1 => $v1) {
                //Determine whether it is a timestamp format
                if (($v1 == $time || preg_match('/_time/', $v1)) && is_numeric($v[$v1])) {
                    if (in_array($v1, $stringArr)) {
                        $tableContent .= '<td style="mso-number-format:\'\@\';" data-tableexport-msonumberformat="\@">' . date('Y-m-d H:i:s', $v[$v1]) . '</td>';
                    } else {
                        $tableContent .= '<td>' . date('Y-m-d H:i:s', $v[$v1]) . '</td>';
                    }
                }else {
                    if (in_array($v1, $stringArr)) {
                        $tableContent .= '<td style="mso-number-format:\'\@\';" data-tableexport-msonumberformat="\@">' . $v[$v1] . ((in_array($v1, $percentArr)) ? '%' : '') . '</td>';
                    } else {
                        $tableContent .= '<td>' . $v[$v1] . ((in_array($v1, $percentArr)) ? '%' : '') . '</td>';
                    }
                }
            }
            $tableContent .= '</tr>';
        }
        $tableContent .= '</tbody></table>';
        return $this->success([
            'excel_content' => $tableContent
        ]);
    }
}
