<?php
$sql = "SELECT i.izin_saglikkurumu,d.durum_ad,u.unvan_ad,p.personel_adsoyad,p.personel_tc,p.personel_sicilno,it.izin_ad,i.izin_tarih,
i.izin_adresi,i.izin_yil,i.izin_baslayis,i.izin_suresi,p.personel_telefon from izin_calisan_haklari ich
inner JOIN durum d on d.durum_id=ich.calisan_statu_id
inner JOIN personel p on p.personel_durumid=d.durum_id
inner JOIN izin i on i.izin_personel=p.personel_id and ich.izin_tur_id=i.izin_turid
inner JOIN unvan u on u.unvan_id=p.personel_unvan 
inner JOIN izin_turleri it on it.izin_turid=ich.izin_tur_id
WHERE p.personel_durum='1'
and i.izin_durum='1'
and p.personel_durumid='1' 
and i.izin_id=$id";
$query = $adminclass->pdoQuery($sql);
?>
<html>
<head>
	<style type="text/css">
		body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:x-small }
		a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
		a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
		comment { display:none;  } 
	</style>
</head>
<body>
<table cellspacing="0" border="0">
	<colgroup span="3" width="24"></colgroup>
	<colgroup width="30"></colgroup>
	<colgroup span="26" width="24"></colgroup>
	<tr>
		<td style="border-bottom: 3px solid #000000" colspan=30 rowspan=3 height="72" align="center" valign=middle><b><font face="Tahoma" color="#000000">T.C.<br>KARAYOLLARI GENEL M&Uuml;D&Uuml;RL&Uuml;&#286;&Uuml;<br>5. B&ouml;lge M&uuml;d&uuml;rl&uuml;&#287;&uuml;</font></b></td>
		</tr>
	<tr>
		</tr>
	<tr>
		</tr>
	<tr>
		<td style="border-top: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="27" align="center" valign=middle><b><font face="Tahoma" color="#000000">REFAKAT iZİN ONAYI</font></b></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="13" align="center" valign=middle><b><font face="Tahoma" color="#000000"><br></font></b></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="36" align="center" valign=middle><font face="Tahoma" color="#000000">KARAYOLLARI 5.B&Ouml;LGE M&Uuml;D&Uuml;RL&Uuml;&#286;&Uuml;NE</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="53" align="left" valign=middle><font face="Tahoma" size=1 color="#000000">          Bir &ouml;rne&#287;i ekli Sa&#287;l&#305;k Kurulu Raporu ile a&#351;a&#287;&#305;da beyan etti&#287;im aile bireyimin tedavisi, refakat&ccedil;i beraberinde uygun g&ouml;r&uuml;lm&uuml;&#351;t&uuml;r. Bu nedenle<br> taraf&#305;ma &hellip;../&hellip;../20..... tarihinden itibaren &hellip;&hellip;.. g&uuml;n/ay Refakat &#304;zni verilmesini arz ederim.   &hellip;../&hellip;../20.....<br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=20 rowspan=2 height="67" align="left" valign=middle><font face="Tahoma" color="#000000">EK: <br>SA&#286;LIK KURULU RAPORU</font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">&#304;MZA</font></td>
		</tr>
	<tr>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">MEMURUN ADI SOYADI</font></td>
		</tr>
	<tr>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 1px solid #000000" colspan=12 height="20" align="center" valign=middle><font face="Tahoma" color="#000000">REFAKAT &#304;ZN&#304; &#304;STEYEN PERSONEL&#304;N</font></td>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">REFAKAT GEREKT&#304;REN A&#304;LE B&#304;REY&#304;N&#304;N </font></td>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=8 align="center" valign=middle><font face="Tahoma" color="#000000">SA&#286;LIK KURULU RAPORUNUN</font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 1px solid #000000" colspan=4 height="20" align="left" valign=middle><font face="Tahoma" color="#000000">ADI VE SOYADI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=8 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=4 align="left" valign=middle><font face="Tahoma" color="#000000">ADI SOYADI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=6 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=5 align="left" valign=middle><font face="Tahoma" color="#000000">TAR&#304;H&#304;</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=3 align="center" valign=middle sdnum="1033;1033;M/D/YYYY"><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 1px solid #000000" colspan=4 height="20" align="left" valign=middle><font face="Tahoma" color="#000000">B&#304;R&#304;M&#304;</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=8 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=4 align="left" valign=middle><font face="Tahoma" color="#000000">YAKINLIK DRC.</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=6 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=5 align="left" valign=middle><font face="Tahoma" color="#000000">SAYISI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 1px solid #000000" colspan=4 height="20" align="left" valign=middle><font face="Tahoma" color="#000000">&Uuml;NVANI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=8 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=4 align="left" valign=middle><font face="Tahoma" color="#000000">T.C.K&#304;ML&#304;K NO</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=6 align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" colspan=5 align="left" valign=middle><font face="Tahoma" color="#000000">REFAKAT S&Uuml;RES&#304;</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="20" align="center" valign=middle><font face="Tahoma" color="#000000">AYNI A&#304;LE B&#304;REY&#304; &#304;&Ccedil;&#304;N DAHA &Ouml;NCE KULLANILAN REFAKAT &#304;Z&#304;NLER&#304;NE &#304;L&#304;&#350;K&#304;N B&#304;LG&#304;LER</font></td>
		</tr>
	<tr>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 2px double #000000" colspan=8 height="20" align="center" valign=middle><font face="Tahoma" color="#000000">A&#304;LE B&#304;REY&#304;N&#304;N</font></td>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=22 align="center" valign=middle><font face="Tahoma" color="#000000">D&Uuml;ZENLENM&#304;&#350; SA&#286;LIK KURULU RAPORU</font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px solid #000000; border-right: 2px double #000000" colspan=5 rowspan=2 height="40" align="center" valign=middle><font face="Tahoma" color="#000000">ADI SOYADI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 rowspan=2 align="center" valign=middle><font face="Tahoma" color="#000000">MEMURA<br>YAKINLI&#286;I</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=2 rowspan=2 align="center" valign=middle><font face="Tahoma" color="#000000">S.NO</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 rowspan=2 align="center" valign=middle><font face="Tahoma" color="#000000">TAR&#304;H&#304;</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=4 rowspan=2 align="center" valign=middle><font face="Tahoma" color="#000000">SAYISI</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=13 align="center" valign=middle><font face="Tahoma" color="#000000">REFAKAT &#304;ZN&#304;</font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=6 align="center" valign=middle><font face="Tahoma" color="#000000">UYGUN G&Ouml;R&Uuml;LEN</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=7 align="center" valign=middle><font face="Tahoma" color="#000000">KULLANILAN</font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 3px solid #000000; border-right: 2px double #000000" colspan=5 rowspan=3 height="60" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 rowspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=2 align="center" valign=middle sdval="1" sdnum="1033;"><font face="Tahoma" color="#000000">1</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=4 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=6 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=7 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=2 align="center" valign=middle sdval="2" sdnum="1033;"><font face="Tahoma" color="#000000">2</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=4 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=6 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=7 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=2 align="center" valign=middle sdval="3" sdnum="1033;"><font face="Tahoma" color="#000000">3</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=4 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 2px double #000000" colspan=6 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px double #000000; border-left: 2px double #000000; border-right: 3px solid #000000" colspan=7 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 2px double #000000; border-bottom: 2px solid #000000; border-left: 3px solid #000000; border-right: 1px solid #000000" colspan=4 rowspan=3 height="60" align="center" valign=middle><font face="Tahoma" color="#000000">YASAL<br>DAYANAK</font></td>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle sdval="1" sdnum="1033;"><font face="Tahoma" color="#000000">1</font></td>
		<td style="border-top: 2px double #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=25 align="left" valign=middle><font face="Tahoma" color="#000000">657 Say&#305;l&#305; Devlet Memurlar&#305; Kanununun 105. Maddesi</font></td>
		</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=2 align="center" valign=middle sdval="2" sdnum="1033;"><font face="Tahoma" color="#000000">2</font></td>
		<td style="border-top: 1px solid #000000; border-bottom: 2px solid #000000; border-left: 1px solid #000000; border-right: 3px solid #000000" colspan=25 rowspan=2 align="left" valign=middle><font face="Tahoma" color="#000000">29.10.2011 tarihli ve 28099 say&#305;l&#305; Resmi Gazetede yay&#305;mlanan&quot; DevletMemurlar&#305;na verilecek<br>Hastal&#305;k Raporlar&#305; ile Hastal&#305;k Refakat &#304;znine &#304;li&#351;kin Usul ve Esaslar Hakk&#305;nda y&ouml;netmelik </font></td>
		</tr>
	<tr>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">KARAYOLLARI 5.B&Ouml;LGE M&Uuml;D&Uuml;RL&Uuml;&#286;&Uuml;NE</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="79" align="left" valign=middle><font face="Tahoma" color="#000000">        S&ouml;z konusu Sa&#287;l&#305;k Kurulu Raporunun &quot;Devlet Memurlar&#305;na Verilecek Hastal&#305;k Hastal&#305;k Raporlar&#305; ile Hastal&#305;k ve<br>Refakat &#304;znine ili&#351;kin Usul ve Esaslar Hakk&#305;nda Y&ouml;netmelik&quot; h&uuml;k&uuml;mlerine uygunlu&#287;u anla&#351;&#305;ld&#305;&#287;&#305;ndan, ad&#305; ge&ccedil;enin ayl&#305;k <br>ve &ouml;zl&uuml;k haklar&#305; korunarak ...../...../20..... tarihinden itibaren .......... g&uuml;n/ay s&uuml;re ile izinli say&#305;lmas&#305;n&#305;,</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=10 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">&#304;mza</font></td>
		<td colspan=10 rowspan=3 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">&#304;mza</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=10 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">Ad&#305; Soyad&#305;</font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">Ad&#305; Soyad&#305;</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=10 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">Birim Amiri Unvan&#305;</font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">B&ouml;lge M&uuml;d&uuml;r Yard&#305;mc&#305;s&#305;</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">O   L   U   R</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">&hellip;../&hellip;../20.....</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">&#304;lhan AYTEK&#304;N</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">B&ouml;lge M&uuml;d&uuml;r&uuml;</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-top: 2px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=22 height="19" align="left" valign=middle><font face="Tahoma" color="#000000">SAYI : 35557330-903.05.02/</font></td>
		<td style="border-right: 3px solid #000000" colspan=8 align="center" valign=middle sdnum="1033;1033;M/D/YYYY"><font face="Tahoma" color="#000000">&hellip;../&hellip;../20&hellip;..</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="center" valign=middle><font face="Tahoma" color="#000000">PERSONEL &#350;UBES&#304; M&Uuml;D&Uuml;RL&Uuml;&#286;&Uuml;NE</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="62" align="left" valign=middle><font face="Tahoma" color="#000000">       Yukar&#305;da ad&#305; soyad&#305; unvan&#305; yaz&#305;l&#305; personelimiz, Refakat &#304;znini kullanarak &hellip;../&hellip;../202 - &hellip;../&hellip;./20..... tarihleri <br> aras&#305;nda g&uuml;n/ay kullanarak, izin bitimi &hellip;../&hellip;./20..... tarihinde g&ouml;revine ba&#351;lam&#305;&#351;t&#305;r.</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=20 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=20 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">Deniz G&Ouml;K</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" colspan=20 height="19" align="center" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-right: 3px solid #000000" colspan=10 align="center" valign=middle><font face="Tahoma" color="#000000">Personel &#350;ubesi M&uuml;d&uuml;rl&uuml;&#287;&uuml;</font></td>
		</tr>
	<tr>
		<td style="border-left: 3px solid #000000" height="19" align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
		<td style="border-right: 3px solid #000000" align="left" valign=middle><font face="Tahoma" color="#000000"><br></font></td>
	</tr>
	<tr>
		<td style="border-top: 2px double #000000; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000" colspan=30 height="19" align="left" valign=middle><font face="Tahoma" color="#000000">NOT: G&ouml;reve ba&#351;lamas&#305; gereken; &hellip;../&hellip;../20..... tarihi, Hafta Sonu Tatilidir/ Resmi Tatildir.</font></td>
		</tr>
</table>
<!-- ************************************************************************** -->
</body>

</html>
