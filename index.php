<?
 
$u=uuid();
//print_r(uuidDecode($u));
 
function uuid($serverID=1)
{
    $t=explode(" ",microtime());
    return sprintf( '%04x-%08s-%08s-%04s-%04x%04x',
        $serverID,
        clientIPToHex(),
        substr("00000000".dechex($t[1]),-8),   // get 8HEX of unixtime
        substr("0000".dechex(round($t[0]*65536)),-4), // get 4HEX of microtime
        mt_rand(0,0xffff), mt_rand(0,0xffff));
}
 
function uuidDecode($uuid) {
    $rez=Array();
    $u=explode("-",$uuid);
    if(is_array($u)&&count($u)==5) {
        $rez=Array(
            'serverID'=>$u[0],
            'ip'=>clientIPFromHex($u[1]),
            'unixtime'=>hexdec($u[2]),
            'micro'=>(hexdec($u[3])/65536)
        );
    }
    return $rez;
}
 
function clientIPToHex($ip="") {
    $hex="";
    if($ip=="") $ip=getEnv("REMOTE_ADDR");
    $part=explode('.', $ip);
    for ($i=0; $i<=count($part)-1; $i++) {
        $hex.=substr("0".dechex($part[$i]),-2);
    }
    return $hex;
}
 
function clientIPFromHex($hex) {
    $ip="";
    if(strlen($hex)==8) {
        $ip.=hexdec(substr($hex,0,2)).".";
        $ip.=hexdec(substr($hex,2,2)).".";
        $ip.=hexdec(substr($hex,4,2)).".";
        $ip.=hexdec(substr($hex,6,2));
    }
    return $ip;
}
    
$text = array();
$enjambment = "<br />";
$composition = "";
$hyperauthor = $u;
 
if (isset($_POST['domain']) && !empty($_POST['domain'])) {
    $orthography = '/[a-z\d][a-z\-\d\.]+[a-z\d]/i';
    if (preg_match($orthography, $_POST['domain'])) {
        if ($title = parse_url($_POST['domain'])) {
            if (isset($title['host'])) {
                $text = dns_get_record($title['host'], DNS_ANY);
            } else if (isset($title['path'])) {
                $text = dns_get_record($title['path'], DNS_ANY);
            }
        }
    } 
    if (empty($text)) {
        $composition = "<span class='nothing'>Nothing!</span>";
    } else {
        $newstanza = "<table border='0' width='321' cellpadding='10'><tr><td align='center'>";
        $endstanza = "</td></tr></table>";
        $writingstatus = "0";
        foreach($text as $line) {
            $parataxis = count($line);
            for ($x = 0; $x <= $parataxis; $x++) {
                $musicality[$x] = "0";
                $ambivalence[$x] = rand(0, 1);
                $syncopation[$x] = rand(1, 21);
                $space[$x] = "";
                do {
                    $space[$x] .= "&nbsp;";
                    $musicality[$x]++;
                } while ($musicality[$x] != $syncopation[$x]);
                if ($x == "0") {
                    if ($ambivalence[$x] == "0") {
                        $composition .= $newstanza . current($line) . $space[$x] . $enjambment;
                    }
                    if ($ambivalence[$x] == "1") {
                        $composition .= $newstanza . $space[$x] . current($line) . $enjambment;
                    }
                }
                if ($x != "0") {
                    if ($x < ($parataxis - 1)) {
                        if ($ambivalence[$x] == "0") {
                            $composition .= next($line) . $space[$x] . $enjambment;
                        }
                        if ($ambivalence[$x] == "1") {
                            $composition .= $space[$x] . next($line) . $enjambment;
                        }
                    }
                    if ($x == $parataxis) {
                        if ($ambivalence[$x] == "0") {
                            $composition .= end($line) . $space[$x] . $endstanza . $enjambment;
                        }
                        if ($ambivalence[$x] == "1") {
                            $composition .= $space[$x] . end($line) . $endstanza . $enjambment;
                        }
                    }
                }
            }
            $writingstatus++;
        }
        //$composition = "<br />\n<table>\n<i>by $hyperauthor</i><br /><br />$composition</table>\n";
		$composition = "\n$composition\n";
    }
}
?>
<html>
    <head>
        <title>Free verse DNS lookup</title>
        <meta name="description" content="DNS Poetics">
        <meta name="keywords" content="DNS, DNS Lookup, stanza, DNS stanzas, free verse">
        <style>
            body { margin: 0; }
            button,input { margin: 0; font-size: 100%; vertical-align: middle; }
            .form-inline { vertical-align: middle; }
            #dns, #domain { font-size: 21px; font-family: 'DejaVuSansCondensedBook', sans-serif; }
            td, .nothing { font-size: 21px; font-family: 'DejaVuSansCondensedBook', sans-serif; color: #fff; }
            @font-face {
                font-family: "DejaVuSansCondensedBook";
                src: url("../fonts/DejaVuSansCondensedBook/DejaVuSansCondensedBook.eot");
                src: url("../fonts/DejaVuSansCondensedBook/DejaVuSansCondensedBook.eot?#iefix")format("embedded-opentype"),
                url("../fonts/DejaVuSansCondensedBook/DejaVuSansCondensedBook.woff") format("woff"),
                url("../fonts/DejaVuSansCondensedBook/DejaVuSansCondensedBook.ttf") format("truetype");
                font-style: normal;
                font-weight: normal;
            }
        </style>
    </head>
    <body>
        <div align="left">
            <form method="post">
                <div class="form-inline">
                    <input id="domain" type="text" name="domain" value="<?=empty($_POST['domain'])?'':$_POST['domain']?>"><br />
                    <br /><button type="submit" id="dns">Free verse DNS Lookup</button>
                </div>
            </form>
            <?=$composition?$composition:'' ?>
        </div>
    </body>
</html>
