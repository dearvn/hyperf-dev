<?PHP
if (!function_exists('curl_get')) {
    //Output the passed data formatted in a human-readable style
    function curl_get($apiUrl = '', $sendData = [], $header = [], $cookie = '')
    {
        if (!empty($sendData)) $apiUrl .= '?' . http_build_query($sendData);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        if(!empty($header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_HEADER, 1);
        }
        if (!empty($cookie))  curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        $response = curl_exec($curl);
        if (empty($response)) Throw new Exception(curl_error($curl), 400);
        curl_close($curl);

        return json_decode($response, true);
    }
}

if (!function_exists('fetch_url')) {
    function fetch_url($url, $type = 0,$post = '',$other_curl_opt = array(), $try_num = 0, $timeout=10, $http_status=0){
        $curl_opt = array(
            CURLOPT_URL => $url,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CONNECTTIMEOUT => 30, //秒
            CURLOPT_TIMEOUT => $timeout, //The maximum number of seconds to allow cURL functions to execute.
        );
        if($type == 1){
            $curl_opt[CURLOPT_POST] = TRUE;
            $curl_opt[CURLOPT_POSTFIELDS] = $post;//username=abc&passwd=bcd,Can also be arrayarray('username'=>'abc','passwd'=>'bcd')
        }

        if($other_curl_opt)
            foreach ($other_curl_opt as $key => $val)
                $curl_opt[$key] = $val;

        $ch = curl_init(); //Initialize curl session
        curl_setopt_array($ch, $curl_opt); //Set session parameters for curl as an array
        $contents = curl_exec($ch); //execute curl session
        if($http_status) $http_status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch); //Close the curl session, its only parameter is the handle returned by the curl init() function
        if(!empty($err)){
            return $err;
        }else{
            if($http_status){
                return $http_status;
            }else{
                return $contents;
            }
        }
    }
}
