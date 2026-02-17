<html>

<head>
    <title>Sözleşmeli Memur İzin Formu </title>
</head>

<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red">
    <table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
        <tr>
            <td width="1060" height="100">
                <p align="left"><b>
                        <font face="Times New Roman"><span
                                style="font-size:16pt;">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;T.C.<br>
                                KARAYOLLARI GENEL MÜDÜRLÜĞÜ<br>
                                &emsp;&emsp;&emsp;&emsp;5. BÖLGE MÜDÜRLÜĞÜ<br>
                            </span></font>
                    </b>
                    <font size="4"></font>
                </p>
            </td>
        </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
        <tr>
            <td width="1060" height="43">
                <p align="center"><b>
                        <font face="Times New Roman"><span style="font-size:16pt;">SÖZLEŞMELİ PERSONEL İZİN FORMU</span>
                        </font>
                    </b>
                    <font size="4"></font>
                </p>
            </td>
        </tr>
    </table>
    <table border="2" bordercolor="black" cellpadding="0" cellspacing="0" width="1066" height="auto">
        <tr>
            <td width="1060" height="469">
                <table border="0" cellpadding="0" cellspacing="0" width="1062" height="412">
                    <tr>
                        <td width="109" height="19">
                            <p align="right"><b>SAYI</b></p>
                        </td>
                        <td width="816" height="19" colspan="3">: 35557330/</td>

                    </tr>

                    <tr>
                        <td width="1062" height="29" colspan="3">
                            <p align="center"><b>
                                    <font face="Times New Roman"><span style="font-size:14pt;">
                                            KARAYOLLARI 5. BÖLGE MÜDÜRLÜĞÜNE</span></font>
                                </b></p>
                        </td>
                    </tr>
                    <tr>
                        <td width="1062" height="67" colspan="3">
                            <p align="left">
                                <font face="Times New Roman"><span style="font-size:14pt;"><br>
                                        &emsp;&emsp;&emsp; Sözleşmeli Personel Çalıştırılmasına İlişkin Esasların 9.
                                        maddesine göre {{ tarih($izin->izin_baslayis) }} tarihinden geçerli olmak üzere
                                        <br>&emsp;&emsp; {{ $izin->izin_yil }} yılı iznime mahsuben
                                        {{ $izin->izin_suresi }} ({{ yaziylasayi($izin->izin_suresi) }}) gün süre ile
                                        izin verilmesini arz ederim.</span>
                                    <span style="font-size:14pt;"><br>&nbsp;</span>
                                </font>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="920" height="33" colspan="2">&nbsp;</td>
                        <td width="140" height="33">
                            <font face="Times New Roman" style="font-size:14pt;"><?= date('d/m/Y') ?></font>
                        </td>
                    </tr>
                    <tr>
                        <td width="1062" height="34" colspan="3">
                            <font face="Times New Roman" style="font-size:14pt;">
                                <p>&nbsp;(......) gün yol izni istiyorum / istemiyorum.<br><br>&nbsp;</b></p>
                            </font>
                        </td>
                    </tr>

                    <tr>
                        <td width="137" height="18"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Sicil No</span></font>
                            </b></td>
                        <td width="925" height="18" colspan="2"><b><span
                                    style="font-size:14pt;">:</span></b><span
                                style="font-size:14pt;">{{ $izin->personel_sicilno }}</span></td>
                    </tr>
                    <tr>
                        <td width="137" height="24"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Adı Soyadı</span>
                                </font>
                            </b></td>
                        <td width="925" height="24" colspan="2"><b><span
                                    style="font-size:14pt;">:</span></b><span
                                style="font-size:14pt;">{{ $izin->personel_adsoyad }}</span></td>
                    </tr>
                    <tr>
                        <td width="137" height="12"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Görevi</span></font>
                            </b></td>
                        <td width="925" height="12" colspan="2"><b><span
                                    style="font-size:14pt;">:</span></b><span style="font-size:14pt;">
                                {{ $izin->unvan_ad }}</span></td>
                    </tr>
                    <tr>
                        <td width="137" height="11"><b>
                                <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;İzin Adresi</span>
                                </font>
                            </b></td>
                        <td width="925" height="11" colspan="2"><b><span
                                    style="font-size:14pt;">:</span></b><span style="font-size:14pt;">
                                        {{ $izin->izin_adresi ?: $izin->personel_adres }}
                                    </span></td>
                    </tr>
                    <!-- <tr>
                <td width="137" height="15"><span style="font-size:14pt;">&nbsp;</span></td>
                <td width="925" height="15" colspan="2"><span style="font-size:14pt;">&nbsp;&nbsp;ADRESDEVAM_CEK</span></td>
            </tr>-->
                </table>

        <tr>
            <td width="1060" height="13">
                <table border="0" cellpadding="0" cellspacing="0" width="1060" height="400">

                    <tr>
                        <td width="1060" height="61" colspan="3">
                            <p align="left">
                                <font style="font-size:14pt;" face="Times New Roman">
                                    <br>

                                    &emsp;&emsp;&emsp;İlgilinin belirtilen tarihler arasında izin kullanmasında sakınca
                                    olmadığını arz ederim.
                                </font>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td width="779" height="49">&nbsp;</td>
                        <td width="141" height="49">&nbsp;</td>
                        <td width="140" height="49">&nbsp;</td>

                    </tr>
                    <tr>
                        <td width="779" height="22">
                            <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;</span></font>
                        </td>
                        <td width="281" height="22" colspan="2">
                            <p align="center">
                                <font face="Times New Roman"><span
                                        style="font-size:14pt;"><b>{{ $ayar->ayar_yonetici }}</b></span></font>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="779" height="10">
                            <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;</span></font>
                        </td>
                        <td width="281" height="10" colspan="2">
                            <p align="center">
                                <font face="Times New Roman"><span
                                        style="font-size:14pt;"><b>{{ $ayar->ayar_yoneticiunvan }}</b></span></font>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="1060" height="100" colspan="3">
                            <p align="center"><b>
                                    <font face="Times New Roman"><span style="font-size:14pt;">UYGUNDUR<br><br>
                                            ...../...../<?= date('Y') ?><br>
                                            <br>
                                            &nbsp;</span></font>
                                </b></p>
                        </td>
                    </tr>
                </table>
            </td>
        <tr>
            <td width="1060" height="auto">
                <table border="0" cellpadding="0" cellspacing="0" width="1061" height="423">
                    <tr>
                        <td width="1061" height="10" colspan="5">
                            <p>&nbsp;</p>
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
                    <tr>
                        <td width="1061" height="51" colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="1061" height="29" colspan="5">
                            <p align="left">
                                <font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Adı geçen
                                    personel yukarıda belirtilen iznini ...../...../<?= date('Y') ?> -
                                    ....../....../<?= date('Y') ?> tarihleri arasında (......) gün kullanmıştır</font>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="1061" height="10" colspan="5">
                            <font style="font-size:14pt;" face="Times New Roman">&emsp;&emsp;&emsp;Bilgilerine arz
                                ederim.</font>
                        </td>
                    </tr>
                    <tr>
                        <td width="1061" height="29" colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="1061" height="57" colspan="5">
                            <!-- <p>&nbsp;</p> -->
                        </td>
                    </tr>

                    <tr>
                        <td width="109" height="16">
                            <font face="Times New Roman"><span style="font-size:14pt;">&nbsp;</span></font>
                        </td>
                        <td width="603" height="1">
                            <font face="Times New Roman"><span style="font-size:14pt;">(.....) Günü yol iznidir</span>
                            </font>
                        </td>

                    </tr>

                    <tr>
                        <td width="779" height="6" colspan="3">&nbsp;</td>
                        <td width="282" height="6" colspan="2">
                            <p align="center"><b>
                                    <font face="Times New Roman"><span
                                            style="font-size:14pt;">{{ $ayar->ayar_mudur }}</span></font>
                                </b></p>
                        </td>
                    </tr>
                    <tr>
                        <td width="779" height="11" colspan="3">&nbsp;</td>
                        <td width="282" height="11" colspan="2">
                            <p align="center"><b>
                                    <font face="Times New Roman"><span
                                            style="font-size:14pt;">{{ $ayar->ayar_mudurunvan }}</span></font>
                                </b></p>
                        </td>
                    </tr>
                    <tr>
                        <td width="779" height="3" colspan="3">&nbsp;</td>
                        <td width="282" height="3" colspan="2">&nbsp;</td>

                    </tr>
                </table>
                <p>&nbsp;<font size="4"></font>
                </p>

            </td>
        </tr>
    </table>
    <font style="font-size:10pt;" face="Times New Roman">Form Stok No:7581967</font><br>
    <table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
        <tr>
            <td width="1060" height="10">
                <font style="font-size:10pt;" face="Times New Roman"><b>1-</b>Merkez Teşkilatında bir nüsha olarak
                    düzenlenir. İzin bitiminde formun II.
                    bölümü doldurulup onaylandıktan sonra ilgilinin şahsi dosyasına konur
                </font>
            </td>
        </tr>
        <tr>
            <td width="1060" height="10">
                <font style="font-size:10pt;" face="Times New Roman"><b>2-</b>Taşra Teşkilatında iki nüsha olarak
                    düzenlenir . İkinci nüsha izne başlandığında,
                    birinci nüsha ise izin bitiminde formun 2. bölümü doldurulup onaylandıktan sonra Personel Şube
                    Müdürlüğüne gönderilir ve her iki
                    nüsha birleştirilerek ilgilinin şahsi dosyasına konulur.</font>
            </td>
        </tr>
        <tr>
            <td width="1060" height="10">
                <font style="font-size:10pt;" face="Times New Roman"><b>3-</b>Form, izin başlangıcından önce kayda
                    alınır.</font>
            </td>
        </tr>
    </table>
</body>

</html>
