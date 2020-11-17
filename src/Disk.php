<?php
namespace Siyahmadde;

use GuzzleHttp\Client;


/**
 * Class Disk
 *
 * @author Fatih Yavuz
 * @source https://github.com/siyahmadde/yandex-disk-api
 */
class Disk
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $base_uri = 'https://cloud-api.yandex.net/v1/disk/';
    /**
     * @var string
     */
    private $auth_type = 'OAuth';
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $token;
    /**
     * @var array
     */
    private $headers;

    /**
     * @var bool true to return already decoded objects
     */
    protected $returnDecoded = false;


    /**
     * Disk constructor.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->client = new Client();
    }

    /**
     * @param bool $value
     */
    public function setReturnDecoded($value = true)
    {
        $this->returnDecoded = $value;
    }

    /**
     * Returns the url where login token can be retrieved.
     *
     * @return string
     */
    public function getLoginToken()
    {
        return 'https://oauth.yandex.com/authorize?response_type=token&client_id=' . $this->id;
    }


    /**
     * This method must be used in callback file.
     * It automatically handles saving token.
     *
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    public static function handleCallback($url)
    {
        echo '<script>';
        echo 'var token = window.location.hash.substr(1);';
        echo 'document.cookie = "token" + "=" + token ;';
        echo '</script>';
        $gem = $_COOKIE['token'];
        $pattern = '/access_token=(.*?)&token_type=(.*?)&expires_in=(\d+)/';
        if (preg_match($pattern, $gem, $data)) {
            $result['access_token'] = $data[1];
            //Calculating when the token will expire. -50 is to eliminate possible problems.
            $time = ($data[3] - 50) + time();
            $date = date('YmdHis', $time);
            $result['expires_at'] = $date;

        } else {
            throw new \Exception('An error occured while parsing token: ' . $gem);
        }

        return $result;
    }

    /**
     * @param string $path
     * @return string
     */
    public function folderInfo($path = '')
    {
        $uri = $this->base_uri . 'resources?path=/' . $path;

        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);

        return $this->_returnData($request);
    }


    //Use the GET method to send a request for data about a Disk.
    /**
     * @return string
     */
    public function aboutDisk()
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/';
        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        return $this->_returnData($request);
    }



    //Use the GET method to send a request for metainformation.
    //The request URL is slightly different for requesting resources located in the Trash.
    //Details: https://tech.yandex.com/disk/api/reference/meta-docpage/
    /*
        Options:
        [& sort=<sorting attribute>]
        [& limit=<limit on the number of resources returned>]
        [& offset=<offset from the beginning of the list>]
        [& fields=<keys needed in the response>]
        [& preview_size=<preview size>]
        [& preview_crop=<whether to crop previews>]
    */
    /**
     * @param $path
     * @param array $options
     * @return string
     */
    public function metaInfo($path, array $options = [])
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/resources?path=' . $path;
        foreach ($options as $key => $option) {
            $uri .= '&' . $key . '=' . $option;
        }

        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        return $this->_returnData($request);


    }

    //The API returns a flat list of all files on the Disk in alphabetical order.
    // The flat list does not reflect the directory structure,
    // so it is convenient for searching for files of a certain type that are spread across different folders.
    // Yandex.Disk detects the file type when uploading each file.
    // Details: https://tech.yandex.com/disk/api/reference/all-files-docpage/

    /*
     * Options:
        [  limit=<number of files in the list>]
        [& media_type=<type of requested files>]
        [& offset=<offset from the beginning of the list>]
        [& fields=<keys needed in the response>]
        [& preview_size=<preview size>]
        [& preview_crop=<whether to crop previews>]
     */
    /**
     * @param array $options
     * @return string
     */
    public function flatList(array $options = [])
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/resources/files';
        $is_first = true;
        foreach ($options as $key => $option) {
            if ($is_first) {
                $uri .= '?' . $key . '=' . $option;
                $is_first = false;
            }
            $uri .= '&' . $key . '=' . $option;
        }

        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        return $this->_returnData($request);
    }

    /**
     * @param $path
     * @param array $parameters
     * @return mixed
     */
    public function getResource($path, $parameters = [])
    {
        $parameters = array_merge($parameters, ['path' => $path]);
        return $this->request('resources', $parameters);
    }


    //The API returns a list of the files most recently uploaded to Yandex.Disk.
    //The list can be filtered by file type (audio, video, image, and so on). Each file type is detected by Disk when uploading.
    //Details: https://tech.yandex.com/disk/api/reference/recent-upload-docpage/
    /*
        Options:
        [  limit=<number of files in the list>]
        [& media_type=<type of requested files>]
        [& fields=<keys needed in the response>]
        [& preview_size=<preview size>]
        [& preview_crop=<whether to crop previews>]
     */
    /**
     * @param array $options
     * @return string
     */
    public function latestUploads(array $options = [])
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/resources/last-uploaded';
        $is_first = true;
        foreach ($options as $key => $option) {
            if ($is_first) {
                $uri .= '?' . $key . '=' . $option;
                $is_first = false;
            }
            $uri .= '&' . $key . '=' . $option;
        }

        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        return $this->_returnData($request);
    }






    ######################## UPLOADING AND DOWNLOADING ############################
    //Details: https://tech.yandex.com/disk/api/reference/upload-docpage/

    /*
     * To upload a file to Disk:
     * Request an upload URL.
     * Upload the file to the given address.
     */

    /*
     * path:
     * The path where you want to upload the file. For example, %2Fbar%2Fphoto.png.
     */

    /*
     * Options:
     * [& overwrite=<overwrite flag>]
     * [& fields=<keys needed in the response>]
     */

    /*
     * overwrite:
     *  Flag for overwriting the file.It is used if the file is uploaded to a folder that already contains a file with the same name.
     *  Accepted values = true || false
     */

    /*
     * fields:
     * List of JSON keys that should be included in the response.
     * Keys that are not included in this list will be discarded when forming the response.
     * If the parameter is omitted, the response is returned in full, without discarding anything.
     * Key names should be comma-separated, and embedded keys should be separated by dots. For example: "name,_embedded.items.path".
     */

    /**
     * @param $path
     * @return mixed
     */
    private function getUploadUrl($path)
    {
        $path = urlencode($path);

        $uri = 'https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . $path;
        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        $response = $request->getBody()->getContents();
        $response = json_decode($response);
        return $response->href;
    }


    /**
     * @param $file
     * @return int
     */
    public function uploadFile($file)
    {
        $uri = $this->getUploadUrl($file);

        $headers['Content-Length'] = filesize($file);
        $finfo = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($finfo, $file);
        $parts = explode(';', $mime);
        $headers['Content-Type'] = $parts[0];
        $headers['Etag'] = md5_file($file);
        $headers['Sha256'] = hash_file('sha256', $file);

        $request = $this->client->request('PUT', $uri,
            [
                'headers' => $headers,
                'body'    => fopen($file, 'rb'),
                'expect'  => true
            ]);
        return $request->getStatusCode();
    }

    public function uploadDir($dir)
    {
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array('.', '..', '__MACOSX'))) {
                if ($value[0] === '.') {
                    continue;
                }
            }
            $this->uploadFile($dir . '/' . $value);
        }
    }


    /**
     * @param $path
     * @return bool
     */
    public function downloadOwnFile($path)
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/resources/download?path=' . $path;
        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        $response = $request->getBody()->getContents();
        $href = json_decode($response)->href;

        return copy($href, $path);
    }

    public function downloadFile($path)
    {
        $uri = 'https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key=' . $path;
        $request = $this->client->request('GET', $uri, ['headers' => $this->headers]);
        $response = $request->getBody()->getContents();
        $href = json_decode($response)->href;
        $name = md5(time());
        copy($href, $name);
        return $name;
    }

    public function saveToDisk($path)
    {
        //$key = urlencode($key);
        $uri = 'https://cloud-api.yandex.net/v1/disk/public/resources/save-to-disk/?public_key=' . $path;
        $request = $this->client->request('POST', $uri, ['headers' => $this->headers]);
        return $this->_returnData($request);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->base_uri;
    }

    /**
     * @param string $base_uri
     */
    public function setBaseUri(string $base_uri)
    {
        $this->base_uri = $base_uri;
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->auth_type;
    }

    /**
     * @param string $auth_type
     */
    public function setAuthType(string $auth_type)
    {
        $this->auth_type = $auth_type;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        $this->headers = ['Authorization' => $this->auth_type . ' ' . $this->token];
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $command
     * @param array $parameters
     * @param string $method
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function _request($command, $parameters, $method)
    {
        $old = http_build_query($parameters);
        $uri = $this->base_uri . $command . '?' . http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
        try {
            return $this->client->request($method, $uri, ['headers' => $this->headers]);
        } catch (\Throwable $e) {
            echo "#1 Failed with path $uri";
            sleep(5);
            try{
            return $this->client->request($method, $uri, ['headers' => $this->headers]);
            } catch (\Throwable $e) {
                echo "#2 Failed with path $uri";
                sleep(5);
                return $this->client->request($method, $uri, ['headers' => $this->headers]);
            }
        }
    }

    /**
     * @param $command
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    public function request($command, $parameters = [], $method = 'GET')
    {
        $request = $this->_request($command, $parameters, $method);
        return $this->_returnData($request);
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     * @return mixed
     */
    protected function _returnData($request)
    {
        $contents = $request->getBody()->getContents();
        if ($this->returnDecoded) {
            $contents = \GuzzleHttp\json_decode($contents);
        }
        return $contents;
    }


}
