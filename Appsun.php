<?php

namespace carono\appsun;

use carono\murl\MUrl;

class Appsun
{
	const OS_WIN = 'win';
	const DIGIT_x32 = 'x32';
	const DIGIT_x64 = 'x64';

	public $url = 'http://appsun.ru';
	public $api;
	public $system_name;
	public $version;

	public function getNextVersion()
	{
		$json = $this->getContent('next-version');
		return $json->data;
	}

	/**
	 * @param        $file
	 * @param        $slug
	 * @param string $os
	 * @param string $digit
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function uploadInstaller($file, $slug, $os = self::OS_WIN, $digit = self::DIGIT_x32)
	{
		if (file_exists($file)) {
			$file = new \CURLFile($file);
			$attr = ["slug" => $slug, "os" => $os, "digit" => $digit];
			$json = $this->getContent('upload-installer', $attr, ["file" => $file]);
			return $json->code == 0;
		} else {
			throw new Exception("File '$file' not found");
		}
	}

	/**
	 * @param       $url
	 * @param array $get
	 *
	 * @return string
	 */
	protected function formUrl($url, $get = [])
	{
		if (is_string($url)) {
			$url = array_filter(explode('/', $url));
		}

		$url = $this->url . join('/', array_merge(['project', $this->system_name], $url));
		if ($this->version) {
			$get["version"] = $this->version;
		}
		return $url . ($get ? $get : '?' . http_build_query($get));
	}

	/**
	 * @param       $url
	 * @param array $get
	 * @param array $post
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function getContent($url, $get = [], $post = [])
	{
		$murl = new MUrl();
		$murl->postAsString = false;
		$murl->postUrlEncode = false;
		$murl->post = $post;
		$url = $this->formUrl($url, $get);
		if ($json = json_decode($murl->getContent($url))) {
			if (!$json->code) {
				return $json;
			} else {
				throw  new Exception($json->message);
			}
		} else {
			throw  new Exception('Connection error');
		}
	}
}