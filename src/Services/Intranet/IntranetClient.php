<?php

namespace App\Services\Intranet;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IntranetClient implements IntranetClientInterface
{

    private $options = ['hydrate' => false, 'deep' => true];

    private $raw_response;

    private $params = [];

    private const CONFIG = [
        'base_uri' => "https://intranet.cpnv.ch",
        'classes' => [
            'endpoint' => '/media/classes',
            'extra' => "sections,students,sections,students_count,final"
        ],
        'students' => [
            'endpoint' => '/media/current_students',
            'extra' => "current_class"
        ],
        'teachers' => [
            'endpoint' => '/media/teachers',
            'extra' => 'current_class_masteries'
        ],
        'rooms' => [
            'endpoint' => '/media/salles',
        ],
        'format' => '.json',
    ];

    public function __construct(
        private string $API_KEY,
        private string $API_SECRET,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function findAllStudents(): ArrayCollection
    {
        return new ArrayCollection($this->makeRequest('students'));
    }

    public function findOneStudent(string $query)
    {
        return $this->makeRequest('students', $query);
    }

    public function findAllTeachers(): ArrayCollection
    {
        return new ArrayCollection($this->makeRequest('teachers'));
    }

    public function findOneTeacher(string $query)
    {
        return $this->makeRequest('teachers', $query);
    }

    public function findAllClasses(): ArrayCollection
    {
        return new ArrayCollection($this->makeRequest('classes'));
    }

    public function findOneClass(string $query)
    {
        return $this->makeRequest('classes', $query);
    }

    private function makeRequest(string $type, string $params = null)
    {
        if($this->options['deep']){
            $this->params['alter[extra]'] = self::CONFIG[$type]['extra'];
        }

        if(!is_null($params)){
                $url = self::CONFIG['base_uri'] . self::CONFIG[$type]['endpoint'] . "/" . $params . self::CONFIG['format'] . "?" . $this->getQueryString();
        } else {
            $url = self::CONFIG['base_uri'] . self::CONFIG[$type]['endpoint'] . self::CONFIG['format'] . "?" . $this->getQueryString();
        }

        return $this->fetch($url);
    }

    private function getQueryString(): string
    {
        $args = $this->params;

        $args['api_key'] = $this->API_KEY;

        ksort($args);

        $request_str = implode('', array_map(
            function ($v, $k) { return sprintf("%s%s", $k, $v); },
            $args,
            array_keys($args)
        ));

        $signature = md5($request_str . $this->API_SECRET);

        $args['signature'] = $signature;

        return implode('&', array_map(
            function ($v, $k) { return sprintf("%s=%s", $k, $v); },
            $args,
            array_keys($args)
        ));
    }


    private function fetch($uri, $method = 'GET')
    {
        try {
            $result = $this->httpClient->request($method, $uri);
            $this->raw_response = $result;

            $this->responseCode = $result->getStatusCode();
            $content = $result->getContent();
        } catch (\Exception $e) {
            return json_decode('{"error": true}');
        }

        return json_decode($content);
    }

    public function getRawResponse()
    {
        if(is_null($this->raw_response)) return null;
        return $this->raw_response;
    }

}