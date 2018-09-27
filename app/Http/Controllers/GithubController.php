<?php

namespace App\Http\Controllers;

use App\UseCases\GitHubRepo;
use Cache;
use Github\Exception\ErrorException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GithubController extends Controller
{
    /** @var GitHubRepo */
    private $githubRepo;
    /** @var Filesystem */
    private $disk;

    /**
     * @param GitHubRepo $github
     */
    public function __construct(GitHubRepo $github)
    {
        $this->githubRepo = $github;
        $this->disk       = Storage::cloud();
    }

    /**
     * @throws \Throwable
     */
    public function push(Request $request)
    {
        if ($request->header('X-Github-Event') != 'push') {
            return;
        }

        if (!$request->isJson()) {
            throw new \InvalidArgumentException('Bad request format.');
        }

        $branch = $this->extractBranch($request->json('ref'));

        foreach ($request->json('commits') as $commit) {
            $this->handleCommit($commit, $branch);
        }
    }

    /**
     * @param $ref
     * @return string
     */
    private function extractBranch($ref)
    {
        return substr(strrchr($ref, '/'), 1);
    }

    /**
     * @param array $commit
     * @param $branch
     * @throws ErrorException
     */
    private function handleCommit($commit, $branch)
    {
        foreach ($commit['added'] as $path) {
            $this->updateFile($path, $branch);
        }
        foreach ($commit['modified'] as $path) {
            $this->updateFile($path, $branch);
        }
        foreach ($commit['removed'] as $path) {
            $this->deleteFile($path, $branch);
        }
    }

    /**
     * @param $path
     * @param $branch
     * @throws ErrorException
     */
    private function updateFile($path, $branch)
    {
        if ($content = $this->githubRepo->downloadContent($path, $branch)) {
            $this->disk->put(
                $fileName = $this->createFileName($branch, $path),
                $content
            );
            Cache::forget($fileName);
        }
    }

    /**
     * @param $path
     * @param $branch
     */
    private function deleteFile($path, $branch)
    {
        $fileName = $this->createFileName($branch, $path);

        if ($this->disk->exists($fileName)) {
            $this->disk->delete($fileName);
            Cache::forget($fileName);
        }
    }

    /**
     * @param $path
     * @param $branch
     * @return string
     */
    private function createFileName($branch, $path)
    {
        return strtolower($branch . '/' . $path);
    }
}
