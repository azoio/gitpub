<?php

namespace App\Helpers;


use Cache;

class CssLoader
{
    /**
     * @param string $style
     * @param bool $cache
     * @return string
     */
    public function loadCss($style, $cache = true)
    {
//        app()->configure('view');
        foreach (config('view.paths', ['']) as $path) {
            $fileName = $path . DIRECTORY_SEPARATOR . $style;
            if (is_file($fileName) && ($result = @file_get_contents($fileName))) {
                if (false == strpos($style, '.min.')) {
                    $result = Cache::rememberForever(
                        md5($fileName . filemtime($fileName)),
                        function () use ($result) {
                            $result = preg_replace('~(/\*.*?\*/)|\r|\n|\t~s', ' ', $result);
                            $result = preg_replace('~\s{2,}~s', ' ', $result);
                            return $result;
                        }
                    );
                }

                return $result;
            }
        }

        return '';
    }

}