<?php
function CURL($url, $data = null, $headers = null, $proxy = null)
{
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => "cookie.txt",
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => isset($data) ? 'POST' : 'GET',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        )
    );
    if (isset($proxy)) {
        curl_setopt($curl, CURLOPT_PROXY, $proxy[0]);
    }
    if (isset($proxy[1]) && $proxy[2]) {
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$$proxy[1]:$$proxy[2]");
    }

    $response = curl_exec($curl);
    $storage = isset(json_decode($response, true)['localStorageData'])?json_decode($response, true)['localStorageData']:'';
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    $t1 = "/state=([^&\r\n]+)/i";
    $t2 = "/Location: ([^\r\n]+)/i";
    preg_match($t1, $headers, $state);
    preg_match($t2, $headers, $localhost);
    if (curl_errno($curl)) {
        echo 'Lỗi CURL: ' . curl_error($curl);
    }

    curl_close($curl);
    return [
        "header" => $headers,
        "url" => isset($body) ? $body : "",
        "state" => isset($state[1]) ? $state[1] : "",
        "storage" => $storage,
        "localhost" => isset($localhost[1]) ? $localhost[1] : '',
    ];
}

$url_ = [
    'Sign' => 'https://auth0.openai.com/authorize?audience=https%3A%2F%2Fapi.openai.com%2Fv1&response_type=code&code_challenge_method=S256&client_id=TdJIcbe16WoTHtN95nyywh5E4yOo6ItG&scope=openid%20email%20profile%20offline_access%20model.request%20model.read%20organization.read%20organization.write&redirect_uri=https%3A%2F%2Fchat.openai.com%2Fapi%2Fauth%2Fcallback%2Fauth0&prompt=login&screen_hint=signup&state=_jX2pdHHtEsloWXvGfKj-6yC3vZnecjxjG-3Vh6aSu8&code_challenge=zDSp8qp1wxa0qRsf0yHrMzgivWlxTtUaWhDWe_OJLIs',
    'add_email' => 'https://auth0.openai.com/u/signup/identifier?state=',
    'add_pass' => 'https://auth0.openai.com/u/signup/password?state=',
    'url_chatgpt' => 'https://auth0.openai.com',
];

$headers = array(
    'authority: auth0.openai.com',
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
    'accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5',
    'cookie: did=s%3Av0%3A68c7fb90-b813-11ee-b9fe-c902aa0753b9.NkRCrSj0LzhTPz3p72pZ9JfvDkqducnudcG8tACf9iU; did_compat=s%3Av0%3A68c7fb90-b813-11ee-b9fe-c902aa0753b9.NkRCrSj0LzhTPz3p72pZ9JfvDkqducnudcG8tACf9iU; _cfuvid=9A3CQS9GuNxdFJHMcsqE6jGHptaUFpEPUpJ54cfCUfU-1705810400486-0-604800000; auth0=s%3Av1.gadzZXNzaW9ugqZoYW5kbGXEQL7ySKtyhyVR-ziyDdoZi-jQgPDvZOG4ePHftLwHuZ-9zO5fxQXk7r1ELkD8dm3X4QC7iq3NQFhLmXb_9Uorg4KmY29va2llg6dleHBpcmVz1_-eEk8AZbCOYq5vcmlnaW5hbE1heEFnZc4PcxQAqHNhbWVTaXRlpG5vbmU.ASGjZc8WYkDhTpdvzG87Sg6josZiN0mWDkGEJBw56NY; auth0_compat=s%3Av1.gadzZXNzaW9ugqZoYW5kbGXEQL7ySKtyhyVR-ziyDdoZi-jQgPDvZOG4ePHftLwHuZ-9zO5fxQXk7r1ELkD8dm3X4QC7iq3NQFhLmXb_9Uorg4KmY29va2llg6dleHBpcmVz1_-eEk8AZbCOYq5vcmlnaW5hbE1heEFnZc4PcxQAqHNhbWVTaXRlpG5vbmU.ASGjZc8WYkDhTpdvzG87Sg6josZiN0mWDkGEJBw56NY; cf_clearance=g4dfLKY03l.1QoTx7i37GO6dXg6px43KFIAOgtVZD5k-1705810402-1-AeSbBdMIgpK83fm+ZDtQztWykMLOwwZ0SYG89BbLcWpQd7WDwmVHqQS08y20KFvrI0bjDOXHhUa6LWBDypFyLXI=; ajs_anonymous_id=e43765be-826d-454e-be84-c5b763a02017; _cfuvid=zZBMN_ktDAjDIL9mNEjP2WdlekNB3pA7nZKXFlmUNzs-1705842490453-0-604800000; __cf_bm=uSgBeA8KkHUhAV0Zy8L8phj0Hw7asLkUg1XQew85YE4-1705845314-1-Af+7mFCt/SqOAowYNb0KrimK4C5yTPJjrxeIlR9oNA1JUgZ58+PwoKpdZFFBXvHPyXnKytf8nD7+/1g7ECfLc0M=; cf_chl_3=565abf262fc97cb; __cf_bm=PYcUde9lrCMTF2IRUUm_wJLJyWQFNKZrsMVvZs5exuc-1705846467-1-AZK2eLWy1MJWJChuo8cQYvw4BQwFzJWy8dzcb7xohXjp4smGxNk+OVBPzK1yI9XhqXvEX9xtPBgjfNa8kwEVVzs=; _cfuvid=L69K.KmgqdOdbfqVhiNWlL4Y.rFsSoJjEOpGKVvsxgA-1705846467182-0-604800000',
    'referer: https://chat.openai.com/',
    'sec-ch-ua: "Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: same-site',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'
);

$email = "oja1pe8v9g@uf.edu.pl";
$pass = "TranDinh1998@";
$proxy = null; //['116.109.10.179:7002','M7FfJ6Kn','KwZJTE2A'];

$b1 = CURL($url_['Sign'], null, $headers, $proxy);
if ($b1['state'] != '') {
    echo "Đang thực hiện add email \n";
    sleep(2);
    $b2 = CURL($url_['add_email'] . $b1['state'], "state=" . $b1['state'] . "&email=$email&action=default", $headers, $proxy);
    if ($b2['state'] != '') {
        echo "Đang Thực Hiện Nhập Password\n";
        sleep(5);
        $b3 = CURL($url_['add_pass'] . $b2['state'], "state=" . $b2['state'] . "&strengthPolicy=low&complexityOptions.minLength=12&email=$email&password=$pass&action=default", $headers, $proxy);
        //print_r($b3);
	echo	"-------------";
        while ($b3['localhost'] != '') {
	    echo $b3['localhost'];
            $b3 = CURL($url_['url_chatgpt'] . $b3['localhost'], '', $headers);
    	    print_r($b3);
	    sleep(1);
	    echo	"-------------";
        }
    }
}