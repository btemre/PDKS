<head>
    <title> Hastalık İzin Formu </title>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
    <tr>
        <td width="1060" height="100">
            <p align="left"><b>
                    <font face="Times New Roman"><span
                            style="font-size:16pt;">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;T.C.
                            <br>
                            ULAŞTIRMA VE ALTYAPI BAKANLIĞI<br>
                            KARAYOLLARI GENEL MÜDÜRLÜĞÜ<br>
                            &emsp;&emsp;&emsp;&emsp;5. BÖLGE MÜDÜRLÜĞÜ<br><br>
                        </span></font>
                </b></p>
        </td>
    </tr>
</table>
<table border="2" width="904" height="1310" bordercolor="black" cellspacing="0" cellpadding="0">
    <td width="895" height="51" colspan="3">
        <table border="0" cellpadding="0" cellspacing="0">
            <p align="center"><b><span style="font-size:16pt;">HASTALIK İZİN FORMU</span></b></p>
        </table>
    </td>

    <tr>
        <td width="93" height="90" rowspan="3">
            <p align="center">
                <font style="writing-mode:rl-tb"><b>HASTALIK<br>
                    </b></font><b>İZNİ<br>
                    KULLANANIN</b>
            </p>
        </td>
        <td width="369" height="29">
            <b>
                <p>&nbsp;ADI SOYADI:</p>
            </b>
        </td>
        <td width="422" height="29"><span style="font-size:14pt;">&nbsp; {{ $izin->personel_adsoyad }}</span></td>
    </tr>
    <tr>
        <td width="369" height="30">
            <b>
                <p>&nbsp;SİCİL NO:</p>
            </b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; {{ $izin->personel_sicilno }}</span></td>
    </tr>
    <tr>
        <td width="369" height="30">
            <b>
                <p>&nbsp;ÇALIŞTIĞI BİRİM:</p>
            </b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; {{ $ayar->ayar_kurum }}</span></td>
    </tr>
    <tr>
        <td width="468" height="30" colspan="2">
            <b>
                <p>&nbsp;&nbsp;SAĞLIK KURUMUNUN ADI:</p>
            </b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; {{ $izin->izin_saglikkurumu }}</span>
        </td>
    </tr>
    <tr>
        <td width="468" height="30" colspan="2">
            <b>
                <p>&nbsp;&nbsp;HASTALIK İZİN SÜRESİ:</p>
            </b>
        </td>
        <td width="422" height="30"><span style="font-size:14pt;">&nbsp; {{ $izin->izin_suresi }}
                ({{ yaziylasayi($izin->izin_suresi) }}) Gün</span></td>
    </tr>
    <tr>
        <td width="895" height="528" colspan="3">
            <table border="0" cellpadding="0" cellspacing="0" width="1061" height="423">


                <tr>
                    <td width="1061" height="51" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="1062" height="67" colspan="5">
                        <p align="left">
                            <font face="Times New Roman" style="font-size:14pt;"><span>

                                    &emsp;&emsp;&emsp;&emsp;&emsp;Toplu İş Sözleşmesinin 60. Maddesine göre
                                    {{ tarih($izin->izin_baslayis) }} tarihinden <br><br>&emsp; &emsp;
                                    başlayarak {{ $izin->izin_suresi }} ({{ yaziylasayi($izin->izin_suresi) }}) gün süre
                                    ile izinli sayılmasını arz ederim.
                                    <br> <br>
                                    &nbsp;</span></font>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td width="1061" height="29" colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td width="779" height="6" colspan="3">&nbsp;</td>
                    <td width="282" height="6" colspan="2">
                        <p align="center"><b>
                                <font face="Times New Roman"><span
                                        style="font-size:14pt;">{{ $ayar->ayar_yonetici }}</span></font>
                            </b></p>
                    </td>
                </tr>
                <tr>
                    <td width="779" height="11" colspan="3">&nbsp;</td>
                    <td width="282" height="11" colspan="2">
                        <p align="center"><b>
                                <font face="Times New Roman"><span
                                        style="font-size:14pt;">{{ $ayar->ayar_yoneticiunvan }}</span></font>
                            </b></p>
                    </td>
                </tr>
                <tr>
                </tr>
            </table>
            <p align="center">
                <font style="font-size:14pt;"><b>UYGUNDUR</b></font>
            </p><br>
            <p align="center">
                <font style="font-size:14pt;"><b>...../...../<?php echo date('Y'); ?><br><br>
        </td>
    </tr>
    <tr>
        <td width="895" height="265" colspan="3">
            <table border="0" cellpadding="0" cellspacing="0" width="1061" height="423">
                <tr>
                    <td width="1061" height="10" colspan="5">

                    </td>
                </tr>



                <tr>
                    <td width="109" height="19">
                        <p align="right"><b>Sayı</b></p>
                    </td>
                    <td width="816" height="19" colspan="3">: 35557330/</td>
                    <td width="136" height="19">......./......../<font face="Times New Roman"><span
                                style="font-size:14pt;"><?= date('Y') ?></span></font>
                    </td>
                </tr>
                <tr>
                    <td width="1061" height="74" colspan="5">
                        <!-- <p>&nbsp;</p> -->
                        <p align="center"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">PERSONEL ŞUBESİ
                                        MÜDÜRLÜĞÜNE</span></font>
                            </b></p>
                    </td>
                </tr>
        </td>
    </tr>

    <tr>
        <td width="109" height="16">
            <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;</span></font>
        </td>
        <td width="603" height="16">
            <font face="Times New Roman"><span style="font-size:14pt;"></span></font>
        </td>
        <td width="349" height="16" colspan="3">
            <font face="Times New Roman"><span style="font-size:14pt;"></span></font>
        </td>
    </tr>
    <tr>
        <td width="1061" height="51" colspan="5">&nbsp;</td>
    </tr>
    <tr>
        <td width="1061" height="29" colspan="5">
            <p align="left">
                <font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Adı geçen personel
                    ...../...../<?= date('Y') ?> ile ....../....../<?= date('Y') ?> tarihleri arasında (......) gün
                    Hastalık iznini kullanarak görevine başlamıştır.</font>
            </p> <br>
        </td>
    </tr>
    <tr>
        <td width="1061" height="10" colspan="5">
            <font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Bilgilerine arz ederim.</font>
        </td>
    </tr>
    <tr>
        <td width="1061" height="29" colspan="5">&nbsp;</td>
    </tr>
    <tr>
        <td width="779" height="6" colspan="3">&nbsp;</td>
        <td width="282" height="6" colspan="2">
            <p align="center"><b>
                    <font face="Times New Roman"><span style="font-size:14pt;">{{ $ayar->ayar_mudur }}</span></font>
                </b></p>
        </td>
    </tr>
    <tr>
        <td width="779" height="11" colspan="3">&nbsp;</td>
        <td width="282" height="11" colspan="2">
            <p align="center"><b>
                    <font face="Times New Roman"><span style="font-size:14pt;">{{ $ayar->ayar_mudurunvan }}</span>
                    </font>
                </b></p> <br><br><br><br>
        </td>
    </tr>
    <tr>
        <td width="779" height="3" colspan="3">&nbsp;</td>
        <td width="282" height="3" colspan="2">&nbsp;</td>

    </tr>
</table>
</td>
</tr>
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
    <tr>
        <td width="1060" height="10">
            <font style="font-size:10pt;" face="Times New Roman"><b>Form Stok No:</b>7582002
            </font>
        </td>
    </tr>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</table>
