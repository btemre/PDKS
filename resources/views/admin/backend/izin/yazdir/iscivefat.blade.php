
<html>
<head>
    <title> İşçi Vefat İzin Formu </title>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red">
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
    <tr>
        <td width="1060" height="100">
            <p align="center"><b><font face="Times New Roman"><span style="font-size:16pt;">T.C.<br>
ULAŞTIRMA VE ALTYAPI BAKANLIĞI<br>
KARAYOLLARI GENEL MÜDÜRLÜĞÜ<br>
5. Bölge Müdürlüğü<br>
</span></font></b><font size="4"></font></p>
        </td>
    </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="43">
    <tr>
        <td width="1060" height="43">
            <p align="center"><b><font face="Times New Roman"><span style="font-size:16pt;">İŞÇİ İZİN FORMU</span></font></b><font size="4"></font></p>
        </td>
    </tr>
</table>
<table border="2" bordercolor="black" cellpadding="0" cellspacing="0" width="1066" height="972">
    <tr>
        <td width="1060" height="457">
            <table border="0" cellpadding="0" cellspacing="0" width="1062" height="412">
                <tr>
                    <td width="46" height="29"><b>&nbsp;SAYI</b></td>
                    <td width="91" height="29">:27290676</td>
                    <td width="725" height="29">&nbsp;</td>
                <!--    <td width="58" height="29"><b>TARIH</b></td> -->
                <tr>
                    <td width="920" height="33" colspan="4">&nbsp;</td>
                    <td width="142" height="33"><?php echo date('d.m.Y'); ?></td>
                </tr>
                </tr>
                <tr>
                    <td width="1062" height="67" colspan="5">
                        <p align="left"><font face="Times New Roman" style="font-size:14pt;"><span>
<p align="center"><font size="4"><b>KARAYOLLARI 5. BÖLGE MÜDÜRLÜĞÜNE</b></font></p> <br>
&emsp;&emsp;&emsp;Toplu İş Sözleşmesinin 61. Maddesinin C bendine göre {{ tarih($izin->izin_baslayis) }} tarihinden geçerli olmak üzere <br>&emsp;{{ $izin->izin_vefat }} vefatından dolayı {{ $izin->izin_suresi }} ({{ yaziylaSayi($izin->izin_suresi) }}) günlük vefat izni verilmesini arz ederim.
<br>
&nbsp;</span></font></p>
                    </td>
                </tr>
                <tr>
                    <td width="920" height="33" colspan="4">&nbsp;</td>
                   <td width="140" height="40"><font face="Times New Roman" style="font-size:14pt;">...../...../<?php echo date("Y");?></font></td>
                </tr>

                <tr>
                    <td width="137" height="17" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;T.C. Kimlik No</span></font></b></td>
                    <td width="925" height="17" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> {{ $izin->personel_tc }}</span></td>
                </tr>
                <tr>
                    <td width="137" height="18" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Sicil No</span></font></b></td>
                    <td width="925" height="18" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> {{ $izin->personel_sicilno }}</span></td>
                </tr>
                <tr>
                    <td width="137" height="24" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Adı, Soyadı</span></font></b></td>
                    <td width="925" height="24" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> {{ $izin->personel_adsoyad }}</span></td>
                </tr>
                <tr>
                    <td width="137" height="12" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Görevi</span></font></b></td>
                    <td width="925" height="12" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> {{ $izin->unvan_ad }}</span></td>
                </tr>
                    <td width="137" height="12" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;Tel</span></font></b></td>
                    <td width="925" height="12" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> 0{{ $izin->personel_telefon }}</span></td>
                </tr>
                <tr>
                    <td width="137" height="11" colspan="2"><b><font face="Times New Roman"><span style="font-size:14pt;">&nbsp;İzin Adresi</span></font></b></td>
                     <td width="925" height="11" colspan="3"><b><span style="font-size:14pt;">:</span></b><span style="font-size:14pt;"> {{ $izin->izin_adresi ?: $izin->personel_adres }}</span></td>
                </tr>
                <tr>
                    <td width="137" height="15" colspan="2"><span style="font-size:14pt;">&nbsp;</span></td>
                   <!-- <td width="925" height="15" colspan="3"><span style="font-size:14pt;">&nbsp;&nbsp;ADRESDEVAM_CEK</span></td>-->
                </tr>
                <tr>
                    <td width="1062" height="55" colspan="5"><br>
<br>
<font face="Times New Roman" style="font-size:14pt;">&emsp;&emsp;&emsp;İlgilinin belirtilen tarihler arasında izin kullanmasında sakınca olmadığını arz ederim.</font><br>
<br>
&nbsp;</td>
                </tr>
                <tr>
                    <td width="862" height="22" colspan="3">&nbsp;</td>
                    <td width="200" height="22" colspan="2">
                        <p align="center"><b><span style="font-size:14pt;"><font face="Times New Roman">{{ $ayar->ayar_yonetici }}</font></span></b></p>
                    </td>
                </tr>
                <tr>
                    <td width="862" height="10" colspan="3">&nbsp;</td>
                    <td width="200" height="10" colspan="2">
                        <p align="center"><b><span style="font-size:14pt;"><font face="Times New Roman">{{ $ayar->ayar_yoneticiunvan }}</font></span></b></p>
                    </td>
                </tr>
                <tr>
                    <td width="862" height="32" colspan="3">&nbsp;</td>
                    <td width="200" height="32" colspan="1">&nbsp;</td>
                </tr>
            </table>
            <p>&nbsp;<font size="4"></font></p>
            <p align="center">&nbsp;</p>
            <p align="center"><font style="font-size:14pt;"><b>UYGUNDUR</b></font></p>
            <p align="center"><font style="font-size:14pt;"><b>...../...../<?php echo date("Y"); ?><br>
<br>
&nbsp;</b></font></p> 
    
    <tr> 
        <td width="1060" height="429">
            <table border="0" cellpadding="0" cellspacing="0" width="1061" height="364">
                <tr>

                    <td width="1061" height="77" colspan="2"> <br>
                        <p align="center"><font size="4"><b>PERSONEL ŞUBESİ MÜDÜRLÜĞÜNE</b></font></p>
                       
                        <p>&nbsp;</p>
                        <p><font face="Times New Roman" style="font-size:14pt;">&emsp;&emsp;&emsp;Adı geçen personel yukarıda belirtilen iznini  ...../...../<?php echo date("Y"); ?>  -  ....../....../<?php echo date("Y"); ?> tarihleri arasında (......) gün kullanmıştır</font><br>
&nbsp;</p>
                    </td>
                </tr>
                <tr>
                    <td width="935" height="119"><font face="Times New Roman" style="font-size:14pt;">&emsp;&emsp;&emsp;Bilgilerinize arz ederim.</font></td>
                </tr>

                <tr>
                    <td width="1061" height="66" colspan="2">&nbsp;</td>
                </tr>
            </table>
            <p>&nbsp;<font size="4"></font></p>
        </td>
    </tr>
</table>
<font style="font-size:10pt;"  face="Times New Roman" >Form Stok No:7583420</font><br>
<font style="font-size:10pt;"  face="Times New Roman" ><b>AÇIKLAMA</b></font>
<table border="0" cellpadding="0" cellspacing="0" width="1060" height="auto">
    <tr>
        <td width="1060" height="10"><font style="font-size:10pt;" face="Times New Roman"><b>1. </b>Merkez Teşkilatında bir nüsha olarak düzenlenir. İzin bitiminde formun 2. bölümü doldurulup onaylandıktan sonra, ilgilinin şahsi dosyasına konur.</font></td>
    </tr>
    <tr>
        <td width="1060" height="10"><font style="font-size:10pt;" face="Times New Roman"><b>2. </b>Taşra Teşkilatında iki nüshha olarak düzenlenir. ikinci nüsha izne başlanıldığında, birinci nüsha ise izin bitiminde formun 2. bölümü doldurulup</font></td>
    </tr>
    <tr>
        <td width="1060" height="10"><font style="font-size:10pt;" face="Times New Roman">&nbsp;&nbsp;&nbsp;onaylandıktan sonra, Personel Şube Müdürlüğüne gönderilir ve her iki nüsha birleştirilerek ilgilinin şahsi dosyasýna konur.</font></td>
    </tr>
    <tr>
        <td width="1060" height="10"><font style="font-size:10pt;" face="Times New Roman"><b>3. </b>Form, izin başlangıcından önce kayda alınır.</font></td>
    </tr>
</table>
</body>
</html>