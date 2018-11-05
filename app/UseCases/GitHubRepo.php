<?php

namespace App\UseCases;


use Github\Client;

class GitHubRepo
{
    private $gitRepoUser = '';
    private $gitRepoName = '';
    private $github;

    /**
     * @param Client $github
     * @param string $gitRepoUser
     * @param string $gitRepoName
     */
    public function __construct(Client $github, $gitRepoUser, $gitRepoName)
    {
        $this->github      = $github;
        $this->gitRepoUser = $gitRepoUser;
        $this->gitRepoName = $gitRepoName;
    }

    /**
     * @param $path
     * @param $branch
     * @return null|string
     * @throws \Github\Exception\ErrorException
     */
    public function downloadContent($path, $branch)
    {
        return $this->github->repo()->contents()->download($this->gitRepoUser, $this->gitRepoName, $path, $branch);

    }

    /**
     * @return array
     */
    private function getFilesList()
    {
        $result = [];
        foreach ($this->getBranches() as $branch) {
            $branch          = $branch['name'];
            $result[$branch] = [];
            foreach ($this->getContents($branch) as $content) {
                if ($content['type'] !== 'file') {
                    continue;
                }
                $result[$branch][] = $content['path'];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getBranches()
    {
        return $this->github->repo()->branches($this->gitRepoUser, $this->gitRepoName);
    }

    /**
     * @param $branch
     * @return array
     */
    private function getContents($branch)
    {
        return $this->github->repo()->contents()->show($this->gitRepoUser, $this->gitRepoName, null, $branch);
    }
}