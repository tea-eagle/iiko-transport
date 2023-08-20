<?php

namespace TeaEagle\IikoTransport;

use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use TeaEagle\IikoTransport\Exceptions\RequestException;
use TeaEagle\IikoTransport\Exceptions\UnsetAttributeException;
use Exception;

class Request
{
    private $urlPrefix = 'https://api-ru.iiko.services/api/1/';
    private $url;
    private $body;
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function setUrl($url)
    {
        $this->url = $this->constructUrl($url);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    private function constructUrl($route, $params = [])
    {
        $route = explode('/', $route);
        $route = array_map('urlencode', $route);
        $route = implode('/', $route);
        $url = $this->urlPrefix . $route;
        $params = http_build_query($params);
        if ($params) {
            if (mb_stripos($url, '?') !== false) {
                $url .= '&' . $params;
            } else {
                $url .= '?' . $params;
            }
        }
        return $url;
    }

	public function post()
    {
        try {
            if (is_null($this->url)) {
                throw new UnsetAttributeException('Request error - unset Url');
            }

            if (is_null($this->body)) {
                throw new UnsetAttributeException('Request error - unset Body');
            }

            $response = $this->app->httpClient->post($this->url, $this->body);

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents());

                if (isset($result->error) && isset($result->message)) {
                    throw new RequestException($result->message, $result->error);
                }

                if (isset($result->errorDescription) && $result->errorDescription) {
                    throw new RequestException($result->errorDescription);
                }

                $data = [
                    'success' => 'true',
                    'url' => $this->url,
                    'body' => $this->body['json'],
                    'result' => $result,
                ];
                // $log->write('file', $data, [
                //     'filename' => 'iiko-order',
                // ]);
                // $log->write('database', $data['body'], [
                //     'url' => $this->url,
                //     'result' => $result,
                // ]);

                return $result;
            } else {
                throw new RequestException("Error");
            }
        } catch (GuzzleRequestException $e) {
            $response = $e->getResponse();
            $message = $response->getBody()->getContents();
            $data = [
                'success' => 'false',
                'url' => $this->url,
                'body' => $this->body,
                'result' => $message,
            ];
            // $log->write('file', $data, [
            //     'filename' => 'iiko-order',
            // ]);
            // $log->write('database', $data['body'], [
            //     'url' => $this->url,
            //     'result' => $message,
            // ]);
            throw new RequestException($message);
        } catch (Exception $e) {
            $data = [
                'success' => 'false',
                'url' => $this->url,
                'body' => $this->body['json'],
                'result' => $e->getMessage(),
            ];
            // $log->write('file', $data, [
            //     'filename' => 'iiko-order',
            // ]);
            // $log->write('database', $data['body'], [
            //     'url' => $this->url,
            //     'result' => $e->getMessage(),
            // ]);
            throw new RequestException($e->getMessage(), $e->getCode());
        }
    }
}