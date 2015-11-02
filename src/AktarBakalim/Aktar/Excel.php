<?php namespace AktarBakalim\Aktar;

use AktarBakalim\Aktar;

class Excel extends Aktar
{
    const XML_UST = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    const XML_ALT = "</Workbook>";

    public $kodlama = 'UTF-8';
    public $baslik = 'Yaprak1';

    public function ustuOlustur()
    {
        $sonuc[] = stripslashes(sprintf(self::XML_UST, $this->kodlama));

        $sonuc[] = "<Styles>";
        $sonuc[] = "<Style ss:ID=\"sDT\"><NumberFormat ss:Format=\"Short Date\"/></Style>";
        $sonuc[] = "</Styles>";

        $sonuc[] = sprintf("<Worksheet ss:Name=\"%s\">\n\t<Table>", htmlentities($this->baslik));

        return implode("\n", $sonuc);
    }

    public function altiOlustur()
    {
        $sonuc[] = "\t</Table>\n</Worksheet>\n";

        $sonuc[] = self::XML_ALT;

        return implode("\n", $sonuc);
    }

    public function satirOlustur($satir)
    {
        $sonuc[] = "\t\t<Row>";

        foreach ($satir as $icerik) {

            $sonuc[] = $this->hucreOlustur($icerik);

        }

        $sonuc[] = "\t\t</Row>";

        return implode("\n", $sonuc);
    }

    private function hucreOlustur($bilgi)
    {
        $stil = '';

        // bilgi eğer 15 rakamdan az ise sayı olarak dikkate alalım
        if (preg_match("/^-?\d+(?:[.,]\d+)?$/", $bilgi) && (strlen($bilgi) < 15)) {

            $type = 'Number';

        } else if (
                preg_match("/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/", $bilgi) &&
                ($timestamp = strtotime($bilgi)) &&
                ($timestamp > 0) &&
                ($timestamp < strtotime('+500 years'))) {

            // tarih için

            $type = 'DateTime';
            $bilgi = strftime("%Y-%m-%dT%H:%M:%S", $timestamp);
            $stil = 'sDT'; // üst içinde tanımlandı, bilgiyi tarih olarak göstersin diye

        } else {

            // diğer durumlarda
            $type = 'String';
        }

        $bilgi = str_replace('&#039;', '&apos;', htmlspecialchars($bilgi, ENT_QUOTES));

        $sonuc[] = empty($stil) ? "<Cell>" : "<Cell ss:StyleID=\"$stil\">";
        $sonuc[] = sprintf("<Data ss:Type=\"%s\">%s</Data>", $type, $bilgi);
        $sonuc[] = "</Cell>";

        return implode("\n", $sonuc);
    }

    public function httpBasliklariniGonder()
    {
        header('Content-Type: application/vnd.ms-excel; charset=' . $this->kodlama);
        header('Content-Disposition: inline; filename="' . basename($this->dosyaadi) . '"');
    }
}