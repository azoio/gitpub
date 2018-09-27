<?php

namespace App\Http\Controllers;


use Cache;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Parsedown;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesController
{
    private $disk;
    /**
     * @var Parsedown
     */
    private $parsedown;

    /**
     * @param Parsedown $parsedown
     */
    public function __construct(Parsedown $parsedown)
    {
        $this->disk      = Storage::cloud();
        $this->parsedown = $parsedown;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view(
            'home',
            [
                'content' => $this->loadContent('', ''),
                'lang'    => ''
            ]
        );
    }

    /**
     * @param string $branch
     * @return \Illuminate\View\View
     */
    public function index($branch = '')
    {
        if (strlen($branch) > 2) {
            return $this->page('', $branch);
        }

        return view(
            'index',
            [
                'content' => $this->loadContent($branch, ''),
                'lang'    => $branch
            ]
        );
    }

    /**
     * @param string $branch
     * @param string $path
     * @return \Illuminate\View\View
     */
    public function page($branch, $path)
    {
        return view('post', [
            'content' => $this->loadContent($branch, $path),
            'lang'    => $branch
        ]);
    }

    /**
     * @param $branch
     * @param $path
     * @return string
     */
    private function loadContent($branch, $path)
    {
        if (empty($path)) {
//            $path = !$branch ? 'readme' : '_index';
            $path = '_index';
        }

        if (empty($branch)) {
            $branch = 'master';
        }

        $pageUri  = $branch . '/' . $path;
        $fileName = $pageUri . '.md';

        return Cache::remember(
            $fileName,
            new \DateTime('1 day'),
            function () use ($fileName, $pageUri) {
                try {
                    $content = $this->disk->get($fileName);
                    $content = $this->parsedown->text($content);
                    $content = preg_replace_callback(
                        '~(href=")[^"]*/blob(/.+?")~i',
                        function ($mathes) {
                            $mathes[2] = str_ireplace('/_index.md', '', $mathes[2]);
                            $mathes[2] = str_ireplace('/readme.md', '', $mathes[2]);
                            $mathes[2] = str_ireplace('.md', '', $mathes[2]);
                            return $mathes[1] . $mathes[2];
                        },
                        $content);
                    return $content;
                }
                catch (FileNotFoundException $e) {
                    throw new NotFoundHttpException('Page not found ' . $pageUri);
                }
            }
        );

    }
}