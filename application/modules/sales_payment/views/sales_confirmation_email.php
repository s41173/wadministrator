<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Payment Confirmation - #<?php echo $socode; ?> </title>
    <style type="text/css">
       
        #top{
            background-color: black;
        }
        .container{
            width: 80%;
            padding: 15px;
        }
        p{
            text-align: left;
            color: #171717;
        }
    </style>
</head>
<body style="margin:0 auto; background-color:#CCCCCCC; font-family: helvetica ; line-height: 25px; font-size: 15px;">
  <center>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2">
        <tr>
            <td align="center" height: 40px; valign="top" id=top>
               <div class="container">
     <img src="<?php echo base_url(); ?>images/property/delica.png" width="40%" alt="logo-delica" style="padding: 2% 0;">
               </div>
            </td>
        </tr>
        <tr>
          <td align="center" valign="top">
            <div class="container" style="background: #FFFFFF ;margin: 1% 0 7% 0;">
                <h2 style="color:#696969" align="left">Salam hangat dari Delica Alumunium!</h2>
                <p> Konfirmasi Anda untuk pembayaran tagihan #<?php echo $socode; ?> telah kami terima. </p>
                <table border="0" cellspacing="0" cellpadding="0" style="margin: 0;">
                    
                    <tr>
                        <td width="40%"> ID Konfirmasi </td>
                        <td width="5%"><span style="margin-left:10px;margin-right:10px">:</span></td>
                        <td width="55%"><span> <?php echo $code; ?> </span></td>
                    </tr>
                    <tr>
                        <td valign="top"> Jumlah Bayar </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
                        <td valign="top"><span> Rp <?php echo $amount; ?>,- </span></td>
                    </tr>
                    
                    <tr>
                        <td valign="top"> Tanggal Bayar </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
                        <td valign="top"><span> <?php echo $dates; ?> </span> </td>
                    </tr>
                    
                    <tr>
                        <td valign="top"> Tahap Pembayaran </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
                        <td valign="top"><span> <?php echo $phase; ?> </span> </td>
                    </tr>
                    
                    <tr>
                        <td valign="top"> Akun Pembayaran </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
                        <td valign="top"><span> <?php echo $bank; ?> </span> </td>
                    </tr>

                    <tr>
                        <td valign="top"> Nama Rekening Pengirim </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
            <td valign="top"><span> <?php echo $sender_name; ?> </span></td>
                    </tr>
                    
                    <tr>
                        <td valign="top"> No Rekening Pengirim </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
            <td valign="top"><span> <?php echo $sender_acc; ?> </span></td>
                    </tr>
                    
                    <tr>
                        <td valign="top"> Bank Rekening Pengirim </td>
                        <td valign="top"><span style="margin-left:10px;margin-right:10px">:</span></td>
            <td valign="top"><span> <?php echo $sender_bank; ?> </span></td>
                    </tr>
                    
                </table>
                
                <br>

<p>Jika order tidak sesuai atau Anda membutuhkan informasi lebih lanjut, silahkan hubungi <br> hotline <?php echo $p_phone; ?>  atau email ke <i><a href="mailto:<?php echo $p_email; ?>"> <?php echo $p_email; ?> </a></i></p>
                <p>Terima kasih dan semoga hari Anda menyenangkan. </p>

                
            </div>
            
          </td>
        </tr>   
  
    </table>
  </center>
</body>
</html>