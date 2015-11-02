<?php namespace AktarBakalim\Aktar;

use AktarBakalim\Aktar;

class CSV extends Aktar
{
    protected function httpBasliklariniGonder()
    {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . basename($this->dosyaadi));
    }

    protected function satirOlustur($satir, $ayrac = ',')
    {
        foreach ($satir as $anahtar => $icerik) {

            $satir[$anahtar] = '"' . str_replace('"', '\"', $icerik) . '"';
        }

        return implode($ayrac, $satir) . "\n";
    }
}