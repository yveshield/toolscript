<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 2020/4/12
 * Time: 10:47
 * File: traverse.php
 */
function traverseDir($filedir)
{
	//打开目录
	$dir = dir($filedir);
	//列出目录中的文件
	while (($file = $dir->read()) !== false) {
		if (is_dir($filedir . "/" . $file)) {
			//递归遍历子目录
			if (($file != ".") AND ($file != "..")) {
				traverseDir($filedir . "/" . $file);
			}
		} elseif (substr($file, -4) == ".mp4") {
			//输出文件完整路径
			echo $filedir . "/" . $file . "\n";
			$result = shell_exec("ffmpeg -i \"" . $filedir . "/" . $file . "\" -af \"volumedetect\" -f null /dev/null 2>&1");
			//解析出平均音量
			if (preg_match('/mean_volume: -(\d+)/', $result, $matches)) {
				print_r($matches);
				if ($matches[1] > 20) {
					//提高平均音量
					print_r(shell_exec("ffmpeg -i \"" . $filedir . "/" . $file . "\" -af volume=" . (int)($matches[1] - 20) . "dB -y out.mp4 2>&1"));
					print_r(shell_exec("mv -f out.mp4 \"" . $filedir . "/" . $file . "\" 2>&1"));
				}
			}
		}
	}
	$dir->close();
}

//遍历目录
traverseDir("./a");