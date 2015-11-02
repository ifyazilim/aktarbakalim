<?php namespace AktarBakalim;

use Exception;

abstract class Aktar
{
    protected $nereye;
    protected $dosyaadi;
    protected $geciciDosyaAdi;
    protected $geciciDosya;
    protected $yaziIcerigi;

    const NEREYE_TARAYICI = 'tarayici';
    const NEREYE_DOSYA = 'dosya';
    const NEREYE_YAZI = 'yazi';

    /**
     * @param string $nereye
     * @param string $dosyaadi
     * @throws Exception
     */
    public function __construct($nereye = self::NEREYE_TARAYICI, $dosyaadi = 'aktarim')
    {
        if ( ! in_array($nereye, [self::NEREYE_TARAYICI, self::NEREYE_DOSYA, self::NEREYE_YAZI]))
            throw new Exception($nereye . ' bilgisi geçerli görünmüyor.');

        $this->nereye = $nereye;
        $this->dosyaadi = $dosyaadi;
    }

    public function ilklendir()
    {
        switch ($this->nereye) {

            case self::NEREYE_TARAYICI:

                $this->httpBasliklariniGonder();

                break;

            case self::NEREYE_DOSYA:

                $this->geciciDosyaAdi = tempnam(sys_get_temp_dir(), 'aktarim');
                $this->geciciDosya = fopen($this->geciciDosyaAdi, 'w');

                break;

            case self::NEREYE_YAZI:

                $this->yaziIcerigi = '';

                break;
        }

        $this->yaz($this->ustuOlustur());
    }

    protected function yaz($bilgi)
    {
        switch ($this->nereye) {

            case self::NEREYE_TARAYICI:

                echo $bilgi;

                break;

            case self::NEREYE_DOSYA:

                fwrite($this->geciciDosya, $bilgi);

                break;

            case self::NEREYE_YAZI:

                $this->yaziIcerigi .= $bilgi;

                break;
        }
    }

    public function satirEkle($satir)
    {
        $this->yaz($this->satirOlustur($satir));
    }

    public function sonuclandir()
    {
        $this->yaz($this->altiOlustur());

        switch ($this->nereye) {

            case self::NEREYE_TARAYICI:

                flush();

                break;

            case self::NEREYE_DOSYA:

                fclose($this->geciciDosya);
                rename($this->geciciDosyaAdi, $this->dosyaadi);

                break;

            case self::NEREYE_YAZI:

                // bir şey yapmaya gerek yok

                break;
        }
    }

    public function yaziIceriginiGetir()
    {
        return $this->yaziIcerigi;
    }

    abstract protected function httpBasliklariniGonder();

    protected function ustuOlustur() { return null; }
    protected function altiOlustur() { return null; }

    abstract protected function satirOlustur($satir);
}