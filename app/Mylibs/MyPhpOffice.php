<?php

namespace App\Mylibs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class MyPhpOffice
{

    protected static $tableFullWidth = 14500;

    protected static $tempPath = 'app/public/temp/';

    public static function setArrayValue($templateProcessor, $key, $array, $count)
    {
        $y = 1;
        for ($x = 0; $x <= $count; $x++) {
            if (count($array) > $x && $array[$x]) {
                $templateProcessor->setValue($key . '' . $y, $array[$x]);
                $y++;
            }
        }
        $z = $y;
        for ($x = $y - 1; $x <= $count; $x++) {
            // dd($z, $x);
            Log::debug($key . '' . $z);
            Log::debug($x);
            $templateProcessor->setValue($key . '' . $z, '');
            $z++;
        }
    }

    public static function exportTableWithPath($name, $rows, $headers, $extension)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $sectionStyle = $section->getStyle();
        $sectionStyle->setOrientation($sectionStyle::ORIENTATION_LANDSCAPE);
        $header = ['size' => 16, 'bold' => true];
        $section->addText(ucfirst($name), $header);
        $table = $section->addTable();
        $headerStyle = ['bgColor' => '182E54'];
        $headerFontStyle = ['color' => 'FFFFFF'];
        $table->addRow();
        $width = 0;
        if (count($headers) > 0) {
            $width = self::$tableFullWidth / count($headers);
            foreach ($headers as $key => $value) {
                $table->addCell($width, $headerStyle)->addText($value, $headerFontStyle);
            }
        } else if (count($rows) > 0) {
            $width = self::$tableFullWidth / count($rows[0]);
        }

        $index = 0;
        foreach ($rows as $row) {
            $table->addRow();
            foreach ($row as $key => $value) {
                $style = $index % 2 !== 0 ? ['bgColor' => 'F4F4F4'] : [];
                $table->addCell($width, $style)->addText(is_array($value) ? join(", ", $value) : $value);
            }
            $index++;
        }

        $now = Carbon::now()->format('Y-m-d_H:i:s');
        $docname = 'export_' . strtolower($name) . '_table_' . $now;
        $path = storage_path(self::$tempPath . $docname . '.docx');
        $phpWord->save($path);

        // Log::debug($path);

        if ($extension === 'pdf') {
            $outdir = storage_path(self::$tempPath);
            // $command = "libreoffice --headless --convert-to pdf $path --outdir $outdir";
            // $process = new Process(['libreoffice', '--headless', "--convert-to pdf $path", "--outdir $outdir"]);
            $process = new Process(["libreoffice", '--headless', '--convert-to', $extension, $path, '--outdir', $outdir]);
            // $process->run();
            try {
                $process->mustRun();
            } catch (ProcessFailedException $exception) {
                Log::error($exception->getMessage());
            }

            unlink($path); // delete the docx file manually
            $path = storage_path(self::$tempPath . $docname . '.' . $extension);
        }

        return $path;
    }

}